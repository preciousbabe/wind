<?php
//set session 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])){
  $_SESSION['email'] = htmlspecialchars($_GET['email']);
  $_SESSION['hash'] = htmlspecialchars($_GET['hash']);
}

if(!isset($_SESSION['email']) || empty($_SESSION['email']) || !isset($_SESSION['hash']) || empty($_SESSION['hash'])){
  header("Location: change-password.php");
  exit();
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
$myForm = new octaValidate('form_reset_pass', OV_OPTIONS);
//define rules for each form input name
$valRules = array(
    "pass" => array(
        ["R", "Your password is required"],
        ["MINLENGTH", 8, "Your password must have a minimum of 8 characters"]
    )
);
//Check if it is a post request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        //begin validation    
        if ($myForm->validateFields($valRules, $_POST) === true) {

            $hash = $_SESSION['hash'];
            $email = $_SESSION['email'];
            $pass = trim($_POST['pass']);

            //check if email is registered already
            $user = $db->SelectOne("SELECT * FROM users WHERE email = :email", ['email' => $email]);

            if (!$user) {
                $_SESSION['formResponse'] = ["success" => false, "message" => "User does not exist"];
                header("Location: change-password.php") . exit();
            }
            //verify hash
            if ($hash !== hash("sha256", $user['pass'])) {
                $_SESSION['formResponse'] = ["success" => false, "message" => "Password reset link is invalid"];
                header("Location: change-password.php") . exit();
            }
            //update password
            $newPass = password_hash($pass, PASSWORD_BCRYPT);
            //update db
            $upd = $db->Update("UPDATE users SET pass = :pass WHERE id = :id", ['pass' => $newPass, 'id' => $user['id']]);

            //return response
            $_SESSION['formResponse'] = ["success" => false, "message" => "Your password has been updated"];
            header("Location: login.php") . exit();
        } else {
            //return errors  
            $_SESSION['formResponse'] = ["success" => false, "message" => "Form validation failed"];
            header("Location: newpass.php") . exit();
        }
    } catch (Exception $e) {
        error_log($e);
        $_SESSION['formResponse'] = ["success" => false, "message" => "A server error has occured"];
        header("Location: newpass.php") . exit();
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
  <title>Update Your Password</title>
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
            <form method="post" id="form_reset" class="justify-content-center align-items-center">
              <input name="hash" value="<?php (isset($_GET) && isset($_GET['hash'])) ? print(htmlspecialchars($_GET['hash'])) : ''; ?>" type="hidden" />
              <input name="email" value="<?php (isset($_GET) && isset($_GET['email'])) ? print(htmlspecialchars($_GET['email'])) : ''; ?>" type="hidden" />
              <div>
                <label for="password" class="form-label small">New Password:</label>
                <input octavalidate="R" minlength="8" type="password" class="form-control border-0 border-bottom"
                  id="inp_pass" name="pass" />
              </div>
              <div>
                <label for="confirm-password" class="form-label small mt-4">Confirm Password:</label>
                <input equalto="inp_pass" type="password" class="form-control border-0 border-bottom"
                  id="inp_con_pass" />
              </div>
              <div class="text-center">
                <button class="btn btn-primary w-100 my-4"> Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/toastr/toastr.min.js"></script>
  <script src="./form.js"></script>
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