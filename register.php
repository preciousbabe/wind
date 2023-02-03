<?php
//set session 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//check if session exists
if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
    header("Location: order.php");
    exit();
}

require(dirname(__FILE__). '/core/functions.php');

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

// var_dump($formResponse);
//use octavalidate
use Validate\octaValidate;

//create new instance
$myForm = new octaValidate('form_register', OV_OPTIONS);
//define rules for each form input name
$valRules = array(
    "address" => array(
        ["R", "Your address is required"],
        ["TEXT", "Your address contains invalid characters"]
    ),
    "username" => array(
        ["R", "Your password is required"],
        ["USERNAME", "Your username contains invalid characters"]
    ),
    "pass" => array(
        ["R", "Your password is required"],
        ["MINLENGTH", 8, "Your password must have a minimum of 8 characters"]
    ),
    "email" => array(
        ["R", "Your Email Address is required"],
        ["EMAIL", "Your Email Address is invalid!"]
    ),
    "phone" => array(
        ["R", "Your phone number is required"],
        // ["PHONE", "Your phone number contains invalid characters"]
    )
);

//validation rule for phone number
// $myForm->customRule('PHONE', "#^\+[0-9]+$#", "Your phone number contains invalid characters");
//Check if it is a post request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        //begin validation    
        if ($myForm->validateFields($valRules, $_POST) === true) {

            //check if email is registered already
            $user = $db->SelectOne("SELECT * FROM users WHERE email = :email OR username = :uname", ['email' => $_POST['email'], 'uname' => $_POST['username']]);

            if ($user) {
                $_SESSION['formResponse'] = ["success" => false, "message" => "A user with this username/email exists already"];
                header("Location: register.php") . exit();
            }
            //generate userid
            $user_id = md5($_POST['username'].( time() * 2));
            //hash the password
            $pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);

            //store data
            $db->Insert("INSERT INTO users (user_id, username, address, phone, email, pass, date_created) VALUES (:uid, :uname, :add, :pho, :email, :pass, :date)", [
                "uid" => $user_id,
                "uname" => $_POST['username'],
                "add" => $_POST['address'],
                "pho" => $_POST['phone'],
                "email" => $_POST['email'],
                "pass" => $pass,
                "date" => time()
            ]);

            //set response
            $_SESSION['formResponse'] = ["success" => true, "message" => "Registration successful"];
            header("Location: login.php") . exit();
        } else {
            //return errors  
            // doReturn(400, false, ["formError" => $myForm->getErrors()]);
            $_SESSION['formResponse'] = ["success" => false, "message" => "Form validation error"];
            header("Location: register.php") . exit();
        }
    } catch (Exception $e) {
        error_log($e);
        // doReturn(500, false, ["message" => "A server error has occured"]);
        $_SESSION['formResponse'] = ["success" => false, "message" => "A sever error has occrued"];
        header("Location: register.php") . exit();
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
  <title>Register form</title>
  <script src="https://code.jquery.com/jquery-3.6.3.min.js"
    integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
  <link href="assets/toastr/toastr.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/octavalidate@latest/native/validate.js"></script>
</head>

<body class="h-100">
  <div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
      <div class="col-10 col-md-6 col-lg-6">
        <div class="row row-col-12 shadow-lg">
          <div class="col-md-5 img-side text-center">
            <img src="./assets/images/tesla1.gif" alt="" class="img-fluid hero-img">
          </div>
          <div class="col-md-7">
            <form method="post" id="form_register" novalidate class="pb-4">
              <div class="text-center my-5">
                <img src="./assets/images/logo.png" alt="logo">
              </div>
              <div class="row row-col-12">
                <div class="col-6">
                  <label for="username" class="form-label small">Username:</label>
                  <input octavalidate="R,USERNAME" type="text" class="form-control border-0 border-bottom"
                    id="inp_Username" name="username" />
                </div>
                <div class="col-6">
                  <label for="phone No" class="form-label small">Phone No:</label>
                  <input octavalidate="R" minlength="11" maxlength="15" type="text" class="form-control border-0 border-bottom" id="inp_phone"
                    name="phone" />
                </div>
              </div>
              <div class="mt-3">
                <label for="email" class="form-label small ">Email:</label>
                <input octavalidate="R,EMAIL" type="email" class="form-control border-0 border-bottom" id="inp_email"
                  name="email" />
              </div>
              <div class="mt-3">
                <label for="address" class="form-label small ">Address:</label>
                <input octavalidate="R,TEXT" type="text" class="form-control border-0 border-bottom" id="inp_address"
                  name="address" />
              </div>
              <div class="row row-col-12 mt-3">
                <div class="col-6">
                  <label for="password" class="form-label small">Password:</label>
                  <input octavalidate="R" minlength="8" type="password" class="form-control border-0 border-bottom"
                    id="inp_pass" name="pass" />
                </div>
                <div class="col-6">
                  <label for="confirm-password" class="form-label small">Confirm Password:</label>
                  <input equalto="inp_pass" type="password" class="form-control border-0 border-bottom"
                    id="inp_con_pass" />
                </div>
              </div>
              <button class="btn btn-primary w-100 mt-4">Register</button>
              <div class="text-center">
                <a href="./login.php" class="text-decoration-none text-center small">Already have an account? Login</a>
              </div>
            </form>
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
      setTimeout( () => {
        window.location.href = "login.php";
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
    $('#form_register').on('submit', (e) => {
      const f = new octaValidate(e.target.id);
      if (!f.validate()) {
        e.preventDefault();
      } else {
        e.currentTarget.submit()
      }
    })
  </script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> -->
</body>

</html>