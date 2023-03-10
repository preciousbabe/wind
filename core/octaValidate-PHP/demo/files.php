<?php
//require library
require '../src/Validate.php';

use Validate\octaValidate;

//initialize new instance of the class
$validate = new octaValidate('form_demo');
//check if post array contains uname
if (isset($_FILES['file'])) {
    //validation rules
    $formRules = array(
        "file" => array(
            ["R"],
            ["ACCEPT-MIME", "audio/mpeg"],
            ["MAXSIZE", "2mb"]
        ),
        "files" => array(
            ["R"],
            ["ACCEPT-MIME", "image/*"],
            ["MAXFILES", "2"],
            ["MAXSIZE", "5mb"]
        )
    );

    //validate form
    if ( $validate->validate($formRules) ) {
        echo "FORM SUBMITTED";
    }
    else {
        //retrieve & display errors
        print('<script>
            window.addEventListener(\'load\', function(){
                showErrors(' . json_encode($validate->getErrors()) . ');
            })
        </script>');
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>octavalidate PHP Demo File</title>

</head>
<body>
    <form id="form_demo" novalidate method="POST" enctype="multipart/form-data">
        <label>Single File Upload</label><br>
        <input type="file" id="inp_sing_file" name="file"><br>
        <label>Multiple Files Upload</label><br>
        <input type="file" id="inp_mul_files" name="files[]" multiple><br><br>
        <button type="submit">Submit</button>
    </form>
    <script src="../frontend/helper.js"></script>
</body>

</html>