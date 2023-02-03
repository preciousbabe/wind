<?php

//set session 
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

//check if session exists
if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

require(dirname(__FILE__) . '/core/functions.php');

//store form response
$formResponse = array(
  "success" => null,
  "message" => null
);

// var_dump($_SESSION);
//reassign values
if (isset($_SESSION['formResponse']) && !empty($_SESSION['formResponse'])) {
  $formResponse['success'] = $_SESSION['formResponse']['success'];
  $formResponse['message'] = $_SESSION['formResponse']['message'];
  //delete session
  unset($_SESSION['formResponse']);
}


$products = array(
  "TYPE 12.5" => array(
    "power" => "12.5 KVa / 10 KW",
    "phase" => "single phase",
    "output" => "220 Volts",
    "amps" => "32 amp",
    "size" => "2 x 2 x 4 / 150 kg"
  ),
  "TYPE 25" => array(
    "power" => "25 KVa / 20 KW",
    "phase" => "single phase",
    "output" => "220 Volts",
    "amps" => "65 amp",
    "size" => "4 x 2 x 4 / 200 kg"
  ),
  "TYPE 60" => array(
    "power" => "50 KVa / 40 KW",
    "phase" => "Three phase",
    "output" => "480 Volts",
    "amps" => "60 amp",
    "size" => "5 x 3 x 4 / 400 kg"
  ),
  "TYPE 75" => array(
    "power" => "75 KVa / 60 KW",
    "phase" => "Three phase",
    "output" => "480 Volts",
    "amps" => "90 amp",
    "size" => "5 x 3 x 4 / 550 kg"
  ),
  "TYPE 125" => array(
    "power" => "125 KVa / 100 KW",
    "phase" => "Three phase",
    "output" => "480 Volts",
    "amps" => "120 amp",
    "size" => "6 x 4 x 6 / 750 kg"
  ),
  "TYPE 150" => array(
    "power" => "150 KVa / 125 KW",
    "phase" => "Three phase",
    "output" => "480 Volts",
    "amps" => "180 amp",
    "size" => "7 x 4 x 7 / 1100 kg"
  ),
  "TYPE 250" => array(
    "power" => "250 KVa / 200 KW",
    "phase" => "Three phase",
    "output" => "480 Volts",
    "amps" => "300 amp",
    "size" => "7 x 4 x 7 / 1500 kg"
  ),
  "TYPE 620" => array(
    "power" => "625 KVa / 500 KW",
    "phase" => "Three phase",
    "output" => "480 Volts",
    "amps" => "600 amp",
    "size" => "10 x 4 x 7 / 3000 kg"
  ),
  "TYPE 1000" => array(
    "power" => "1000 KVa / 800 KW",
    "phase" => "Three phase",
    "output" => "480 Volts",
    "amps" => "1200 amp",
    "size" => "12 x 5 x 10 / 5000 kg"
  )
);

// foreach($products as $name => $data){
//   $db->Insert("INSERT INTO products (name, power, phase, output, amps, size) VALUES (:n, :po, :ph, :o, :a, :s)",[
//     'n' => $name,
//     'po' => $data['power'],
//     'ph' => $data['phase'],
//     'o' => $data['output'],
//     'a' => $data['amps'],
//     's' => $data['size']
//   ]);
// }

$prod = $db->SelectAll("SELECT * FROM products");

//use octavalidate
use Validate\octaValidate;

//create new instance
$myForm = new octaValidate('', OV_OPTIONS);
//define rules for each form input name
$valRules = array(
    "product" => array(
        ["R", "The product is required"],
        ["TEXT"]
    )
);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
      //begin validation    
      if ($myForm->validateFields($valRules, $_POST) === true) {

          ///////////////////////////////send mail
          
          $emailTemp = file_get_contents('core/emails/new_order.html');
          $dynamic = array(
              "PRODUCT_NAME" => $_POST['product'],
              "USERNAME" => (!empty($_SESSION['user']['username'])) ? $_SESSION['user']['username'] : "Not Available",
              "EMAIL" => (!empty($_SESSION['user']['email'])) ? $_SESSION['user']['email'] : "Not Available",
              "PHONE" => (!empty($_SESSION['user']['phone'])) ? $_SESSION['user']['phone'] : "Not Available",
              "ADDRESS" => (!empty($_SESSION['user']['address'])) ? $_SESSION['user']['address'] : "Not Available"
          );
          //replace placeholders with actual values
          $body = doDynamicEmail($dynamic, $emailTemp);
          //send mail
          sendMail('admin@windelectric.com.ng', '', "You Have A New Order", $body);
          //return response
          $_SESSION['formResponse'] = ["success" => true, "message" => "Thank you for your order!"];
          //set session for 
          header("Location: newpass.php") . exit();
      } else {
          //return errors  
          $_SESSION['formResponse'] = ["success" => false, "message" => "Form validation failed"];
          header("Location: order.php") . exit();
      }
  } catch (Exception $e) {
      error_log($e);
      $_SESSION['formResponse'] = ["success" => false, "message" => "A server error has occured"];
      header("Location: order.php") . exit();
  }
}

?>
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./form.css" />
  <link rel="shortcut icon" href="./assets/images/logo.png">
  <title>Order form</title>
  <script src="https://code.jquery.com/jquery-3.6.3.min.js"
    integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
  <link href="assets/toastr/toastr.min.css" rel="stylesheet" />
</head>

<body class="h-100">
  <div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
      <div class="col-10 col-md-6 col-lg-6">
        <div class=" shadow-lg row py-3">
          <div class="col-12">
            <div class="text-center my-5">
              <img src="./assets/images/logo.png" alt="logo" class="logo">
            </div>
            <form id="form_create_order" method="post">
            <label for="inp_product_type">Select Product Type</label>
            <select octavalidate="R,TEXT" name="product" id="inp_product_type" class="form-select form-select-lg mb-3">
              <option value="">Select One</option>
              <?php 
                foreach($prod as $key => $p){
              ?>
                <option value="<?php print($p['name']); ?>"><?php print($p['name']); ?></option>
                <?php
              }
              ?>
            </select>
            <div class="alert alert-info">
              <table class="table">
                <tbody>
                  <tr>
                    <th>Power</th>
                    <td id="power">12.5 KVa / 10 KW</td>
                  </tr>
                  <tr>
                    <th>Phase</th>
                    <td id="phase">Single Phase</td>
                  </tr>
                  <tr>
                    <th>Output</th>
                    <td id="output">220 Volts</td>
                  </tr>
                  <tr>
                    <th>Apms</th>
                    <td id="amps">32 Amps</td>
                  </tr>
                  <tr>
                    <th>Dimension</th>
                    <td id="dimension">2 x 2 x 3 / 150 kg</td>
                  </tr>
                </tbody>
              </table>
            </div>
            </form>
            <!-- <button class="btn btn-primary my-3">Order Now!</button> -->
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
              Order
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <p class="lead">Are you sure you want to place an order for this product?<br /><br /> If yes, we will send you a mail with the price of this product shortly</p>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" form="form_create_order" class="btn btn-primary" data-bs-dismiss="modal">Yes, I'm sure</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script src="assets/toastr/toastr.min.js"></script>
  <?php
  if (
    isset($formResponse['success']) && is_bool($formResponse['success'])
    && isset($formResponse['message']) && !empty($formResponse['message'])
  ) {
    if ($formResponse['success'] === true):
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      toastr.success("<?php print($formResponse['message']); ?>")
      setTimeout(() => {
        window.location.href = "order.php";
      }, 3000)
    })
  </script>
  <?php elseif ($formResponse['success'] === false):

        ?>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      toastr.error("<?php print($formResponse['message']); ?>")
    })
  </script>
  <?php endif;
  }
        ?>
  <script>
    $('#form_login').on('submit', (e) => {
      const f = new octaValidate(e.target.id);
      if (!f.validate()) {
        e.preventDefault();
      } else {
        e.currentTarget.submit()
      }
    })
  </script>
  <script>
    const products = <?php print(json_encode($prod)); ?>;

    // console.log(products)
            
document.addEventListener('DOMContentLoaded', ()=>{

  let getIndex = (name) => {
    return products.findIndex(el => {
      return (el.name === name)
    })
  }

    const inpElem = $('#inp_product_type')[0];
    $(inpElem).html('');
    //loop through products
    products.forEach(item => {
      inpElem.innerHTML += `<option value="${item.name}">${item.name}</option>`;
    })

    //event listener
    $(inpElem).on('change', (e) => {
        const val = e.target.value;
        $('#power').html(products[getIndex(val)].power)
        $('#phase').html(products[getIndex(val)].phase)
        $('#output').html(products[getIndex(val)].output)
        $('#amps').html(products[getIndex(val)].amps)
        $('#dimension').html(products[getIndex(val)].dimension)
    })

})
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>