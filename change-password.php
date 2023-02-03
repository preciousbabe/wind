<?php
//set session 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require(dirname(__FILE__). '/core/functions.php');

//check if session exists
if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
    header("Location: order.php");
    exit();
}

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

//use octavalidate
use Validate\octaValidate;

//create new instance
$myForm = new octaValidate('form_reset', OV_OPTIONS);
//define rules for each form input name
$valRules = array(
    "email" => array(
        ["R", "Your Email Address is required"],
        ["EMAIL", "Your Email Address is invalid!"]
    )
);
//Check if it is a post request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        //begin validation    
        if ($myForm->validateFields($valRules, $_POST) === true) {

            //check if email is registered already
            $user = $db->SelectOne("SELECT * FROM users WHERE email = :email", ['email' => $_POST['email']]);

            if (!$user) {
                $_SESSION['formResponse'] = ["success" => false, "message" => "User does not exist"];
                header("Location: change-password.php") . exit();
            }
            //generate reset link
            //try to add time limit too
            $link = ORIGIN . '/newpass.php?email=' . $user['email'] . '&hash=' . hash("sha256", $user['pass']);

            ///////////////////////////////send mail
            
            $emailTemp = file_get_contents('core/emails/reset_link.html');
            $dynamic = array(
                "UNAME" => (!empty($user['username'])) ? $user['username'] : "Esteemed Client",
                "RESET_LINK" => $link
            );
            //replace placeholders with actual values
            $body = doDynamicEmail($dynamic, $emailTemp);
            //send mail
            sendMail($_POST['email'], '', "Reset Your Password", $body);
            //return response
            $_SESSION['formResponse'] = ["success" => true, "message" => "Please check your email for instructions"];
            //set session for 
            header("Location: newpass.php") . exit();
        } else {
            //return errors  
            $_SESSION['formResponse'] = ["success" => false, "message" => "Form validation failed"];
            header("Location: change-password.php") . exit();
        }
    } catch (Exception $e) {
        error_log($e);
        $_SESSION['formResponse'] = ["success" => false, "message" => "A server error has occured"];
        header("Location: change-password.php") . exit();
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
  <title>Reset Your Password</title>
  <script src="https://code.jquery.com/jquery-3.6.3.min.js"
    integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
  <link href="assets/toastr/toastr.min.css" rel="stylesheet" />
</head>

<body class="h-100">
  <div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
      <div class="col-10 col-md-6 col-lg-6">
        <div class="row row-col-12 shadow-lg">
          <div class="img-side col-md-7">
            <img src="./assets/images/tesla1.gif" alt="" class="img-fluid hero-img">
          </div>
          <div class="col-md-5 my-auto form-side">
            <div class="text-center my-5">
              <img src="./assets/images/logo.png" alt="logo" class="logo">
            </div>
            <form method="post" id="form_reset" class="justify-content-center px-2 align-items-center">
              <div>
                <label for="email" class="form-label small ">Email:</label>
                <input octavalidate="R,EMAIL" type="email" class="form-control border-0 border-bottom" id="inp_email" name="email" />
              </div>
              <div class="text-center">
                <button class="btn btn-primary w-100 my-4">Submit</button>
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
    $('#form_reset').on('submit', (e) => {
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