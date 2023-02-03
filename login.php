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

// var_dump($formResponse);
//use octavalidate
use Validate\octaValidate;

//create new instance
$myForm = new octaValidate('form_login', OV_OPTIONS);
//define rules for each form input name
$valRules = array(
  "pass" => array(
    ["R", "Your password is required"],
    ["MINLENGTH", 8, "Your password must have a minimum of 8 characters"]
  ),
  "username" => array(
    ["R", "Your username is required"],
    ["USERNAME", "Your username contains invalid characters!"]
  )
);
//Check if it is a post request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    //begin validation    
    if ($myForm->validateFields($valRules, $_POST) === true) {

      //check if email is registered already
      $user = $db->SelectOne("SELECT * FROM users WHERE username = :uname", ['uname' => $_POST['username']]);

      if (!$user) {
        $_SESSION['formResponse'] = ["success" => false, "message" => "User does not exist"];
        header("Location: login.php") . exit();
      }

      //compare password
      if (password_verify($_POST['pass'], $user['pass']) === false) {
        $_SESSION['formResponse'] = ["success" => false, "message" => "You have provided an invalid password"];
        header("Location: login.php") . exit();
      } else {
        $_SESSION['user'] = array(
          "username" => (!empty($user['username'])) ? $user['username'] : null,
          "user_id" => (!empty($user['user_id'])) ? $user['user_id'] : null,
          "email" => (!empty($user['email'])) ? $user['email'] : null
        );
        //set response
        $_SESSION['formResponse'] = ["success" => true, "message" => "Login successful"];
        header("Location: order.php") . exit();
      }
    } else {
      //return errors  
      // doReturn(400, false, ["formError" => $myForm->getErrors()]);
      $_SESSION['formResponse'] = ["success" => false, "message" => "Form validation error"];
      header("Location: login.php") . exit();
    }
  } catch (Exception $e) {
    error_log($e);
    // doReturn(500, false, ["message" => "A server error has occured"]);
    $_SESSION['formResponse'] = ["success" => false, "message" => "A sever error has occrued"];
    header("Location: login.php") . exit();
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
  <title>Login</title>
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
          <div class="img-side col-md-7">
            <img src="./assets/images/tesla1.gif" alt="" class="img-fluid hero-img">
          </div>
          <div class="col-md-5 my-auto form-side">
            <div class="text-center my-5">
              <img src="./assets/images/logo.png" alt="logo" class="logo">
            </div>
            <form method="post" id="form_login" class="justify-content-center px-2 align-items-center">
              <div>
                <label for="username" class="form-label small ">Username:</label>
                <input octavalidate="R,USERNAME" type="text" class="form-control border-0 border-bottom"
                  placeholder="Username" id="inp_Username" name="username" />
              </div>
              <div class="div">
                <label for="password" class="form-label small mt-5">Password:</label>
                <input octavalidate="R" minlength="8" type="password" class="form-control border-0 border-bottom"
                  placeholder="Password" id="inp_pass" name="pass" />
              </div>
              <div class="text-center">
                <button class="btn btn-primary w-100 my-4">Login</button>
                <a href="./register.php" class="text-decoration-none text-center small">Don't have an account?<br> Sign
                  Up</a><br>
                <a href="./change-password.php" class="text-decoration-none text-center small">Forget Password</a>
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
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> -->
</body>

</html>