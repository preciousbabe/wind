<?php

//set session 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

//use octavalidate
use Validate\octaValidate;

//create new instance
$myForm = new octaValidate('', OV_OPTIONS);
//define rules for each form input name
$valRules = array(
    "name" => array(
        ["R", "Your Name is required"],
        ["ALPHA_SPACES"]
    ),
    "email" => array(
        ["R", "Your Email Address is required"],
        ["EMAIL"]
    ),
    "phone" => array(
        ["R", "Your Phone Number is required"],
        ["DIGITS"]
    ),
    "message" => array(
        ["R", "Your message is required"],
        ["TEXT"]
    )
);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        //begin validation    
        if ($myForm->validateFields($valRules, $_POST) === true) {
            ///////////////////////////////send mail

            $emailTemp = file_get_contents('core/emails/contact.html');
            $dynamic = array(
                "NAME" => $_POST['name'],
                "EMAIL" => $_POST['email'],
                "PHONE" => $_POST['phone'],
                "MESSAGE" => $_POST['message'],
            );
            //replace placeholders with actual values
            $body = doDynamicEmail($dynamic, $emailTemp);
            //send mail
            sendMail('admin@windelectric.com.ng', 'Admin', "I need Your Assistance", $body);
            //return response
            $_SESSION['formResponse'] = ["success" => true, "message" => "Message has been sent successfully!"];
            //set session for 
            header("Location: index.php") . exit();
        } else {
            //return errors  
            $_SESSION['formResponse'] = ["success" => false, "message" => "Form validation failed"];
            header("Location: index.php") . exit();
        }
    } catch (Exception $e) {
        error_log($e);
        $_SESSION['formResponse'] = ["success" => false, "message" => "A server error has occured"];
        header("Location: index.php") . exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/images/logo.png">
    <script src="https://kit.fontawesome.com/e6d5545f07.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./style.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@1,300&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,400&display=swap"
        rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <link href="assets/toastr/toastr.min.css" rel="stylesheet" />
    <script src="https://unpkg.com/octavalidate@latest/native/validate.js"></script>
    <title>Wind Electricity Nigeria</title>
    <style>
        .octavalidate-txt-error {
            font-size: inherit;
        }
    </style>
</head>

<body>
    <!-- header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg bg-light mb-5">
            <div class="container">
                <a href="./index.php" class="navbar-brand">
                    <img src="./assets/images/logo.png" alt="logo">
                </a>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navmenu" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navmenu">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link text-primary fw-1 fs-2">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="#team" class="nav-link text-primary fw-1 fs-2">Team</a>
                        </li>
                        <li class="nav-item">
                            <a href="#about" class="nav-link text-primary fw-1 fs-2">About</a>
                        </li>
                        <li class="nav-item">
                            <a href="#features" class="nav-link text-primary fw-1 fs-2">Features</a>
                        </li>
                        <li class="nav-item">
                            <a href="#products" class="nav-link text-primary fw-1 fs-2">Products</a>
                        </li>
                        <li class="nav-item">
                            <a href="#contact" class="nav-link text-primary fw-1 fs-2">Contact us</a>
                        </li>
                    </ul>
                </div>
        </nav>
        </div>

        <!-- hero section -->
        <div class="container">
            <div class="row d-flex flex-md-row-reverse mt-4 align-items-center hero">
                <div class="col-md">
                    <img src="./assets/images/tesla1.gif" alt="lady smiling" class="img-fluid hero-img">
                </div>
                <div class="col-md">
                    <h1 class="hero-tagline">Reliable & Sustainable <span>Wind Generated</span> Electricity</h1>
                    <h4 class="h2 text-light">Energy powers dreams, we create that energy for you...</h4>
                    <div class="mt-5">
                        <a href="./register.php" class="btn-tagline mt-5">Get Started</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- team -->
    <section id="team" class="text-center my-5 team">
        <div class="container">
            <h1 class="h1 text-center">Team</h1>
            <p class="lead text-center">Windelectric Nigeria consists of core professionals with years of quality
                work<br> experience and expertise.</p>
            <div class="row row-col-2 align-items-center team team-img">
                <div class="col-lg text-center">
                    <img src="./assets/images/team-1.jpg" alt="photo" class="photo">
                    <p class="lead">Engineer Tunji Ishola <br>Lead Engineer / C.E.O</p>
                </div>
                <div class="col-lg text-center">
                    <img src="./assets/images/my pastor.jfif" alt="photo" class="photo">
                    <p class="lead">Engr Jeffery Uzodo <br>MD/COO</p>
                </div>
            </div>
        </div>
        <!-- </div> -->
    </section>

    <!-- about section -->
    <section class="about py-3" id="about">
        <div class="container">
            <h1 class="text-center py-3">ABOUT THE PRODUCT</h1>
            <div class="row">
                <div class="col-lg">
                    <div class="about-image">
                        <img src="./assets/images/clean electric.jpeg" alt="product image">
                    </div>
                </div>
                <div class="col-lg">
                    <div class="about-content">
                        <p>Windelectric is the leader in the new energy innovation,
                            changing the system of electrical energy generation and efficiency.</p>
                        <br>
                        <p>
                            Windelectric is a credible Nigerian company based in Ogun state, Nigeria and its team is
                            comprised of individuals with wide experience in power generation technologies. We have
                            explored
                            the open source journals of one of the greatest inventors of all time, Nicola Tesla to
                            design &
                            model the Windelectric Magnetic Power Generator (WMPG) .</p>
                        <br>
                        <p><i class="fas fa-lightbulb"></i>Redefining Electrical Power Energy</p>
                        <p><i class="fas fa-fire"></i>WMPG Zero Emission Energy</p>
                        <p><i class="fas fa-bolt"></i>Free Energy generation Breakthrough</p>
                        <a href="./learnmore.html"><button class="btn">Learn More</button></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- features section -->
    <section class="features" id="features">
        <div class="container">
            <div class="text-center">
                <h1>Our Outstanding Core Features</h1>
            </div>
            <div class="row">
                <div class="col-lg">
                    <div class="features-image">
                        <img src="./assets/images/handwithlight.jpeg" alt="image">
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class=" box col">
                            <h3 class="h3"><i class="fa-solid fa-cloud"></i>Zero Emission</h3>
                            <p>The WMPG is an absolute Zero Emission machine,
                                reducing the anthropological Carbon Footprint because it doesn't burn fossil fuels.</p>
                        </div>
                        <div class="box">
                            <h3 class="h3"><i class="fa-sharp fa-solid fa-fire-flame-curved"></i>Fuelless</h3>
                            <p>The WMPG requires no lubricant, coolant, fuel or any material
                                to aid combustion, cool the moving parts or reduce friction.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="box col">
                            <h3 class="h3"><i class="fa-solid fa-hand-holding-dollar"></i>Economic</h3>
                            <p>The WMPG, saves you recurrent expenditure on fuel, coolants and lubricants.</p>
                        </div>
                        <div class="box">
                            <h3 class="h3"> <i class="fa-solid fa-headphones"></i>Noiseless</h3>
                            <p>The WMPG works silently. It has no combustion process and a minimal frictional process
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="box col">
                            <h3 class="h3"><i class="fa-solid fa-sync-alt"></i>Durable</h3>
                            <p> The perpetual motion of the WMPG magnetic wheel can run for
                                years uninterrupted with an expected lifespan of 35 years!</p>
                        </div>
                        <div class="box">
                            <h3 class="h3"><i class="fa-solid fa-robot"></i>Intelligent</h3>
                            <p>The WMPG is a smart machine, equipped with Artificial
                                Intelligence to achieve advaned control and monitoring</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- product section -->

    <section class="products" id="products">
        <h1 class="text-center h1"></h1>
        <section id="products">
            <div class="container">
                <h1 class="h1 text-center py-4">Product Specifications</h1>
                <div class="row text-center gy-4 mb-5">
                    <div class="col-lg-3 col-6 fw-bolder fs-2 fs-1">MODEL</div>
                    <div class="col-lg-3 col-6 fw-bolder fs-2 fs-1">POWER</div>
                    <div class="col-lg-3 col-6 fw-bolder fs-2 fs-1">OUTPUT</div>
                    <div class="col-lg-3 col-6 fw-bolder fs-2 fs-1">DIMENSION</div>
                </div>
                <div class="row text-center mb-5">
                    <div class="col-md-3 col-sm-12 fw-bolder fs-1 ">TYP 12.5</div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">12.5KVa/10KW<br><span class="fs-3 fw-light">Single
                            Phase</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">220 volts<br> <span class="fs-3 fw-light">32
                            Amps</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">2 x 2 x 3<br> <span class="fs-3 fw-light">150kg</span>
                    </div>
                </div>
                <div class="row text-center mb-5">
                    <div class="col-md-3 col-sm-12 fw-bolder fs-1">TYP 25</div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">25KVa/20KW<br><span class="fs-3 fw-light">Single
                            Phase</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">220 volts<br> <span class="fs-3 fw-light">65
                            Amps</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">4 x 2 x 4<br> <span class="fs-3 fw-light">200kg</span>
                    </div>
                </div>
                <div class="row text-center mb-5">
                    <div class="col-md-3 col-sm-12 fw-bolder fs-1">TYP 60</div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">50KVa/40KW<br><span class="fs-3 fw-light">Three
                            Phase</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">480 volts<br> <span class="fs-3 fw-light">60
                            Amps</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">5 x 3 x 4<br> <span
                            class="fs-3 fw-light">400kg</span></div>
                </div>
                <div class="row text-center mb-5">
                    <div class="col-md-3 col-sm-12 fw-bolder fs-1">TYP 75</div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1  ">75KVa/60KW<br><span class="fs-3 fw-light">Three
                            Phase</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">480 volts<br> <span class="fs-3 fw-light">90
                            Amps</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">5 x 3 x 4<br> <span class="fs-3 fw-light">550kg</span>
                    </div>
                </div>
                <div class="row text-center mb-5">
                    <div class="col-md-3 col-sm-12 fw-bolder fs-1">TYP 125</div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">125KVa/100KW<br><span class="fs-3 fw-light">Three
                            Phase</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">480 volts<br> <span class="fs-3 fw-light">120
                            Amps</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">6 x 4 x 6<br> <span class="fs-3 fw-light">750kg</span>
                    </div>
                </div>
                <div class="row text-center mb-5">
                    <div class="col-md-3 col-sm-12 fw-bolder fs-1">TYP 150</div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">150KVa/125KW<br><span class="fs-3 fw-light">Three
                            Phase</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">480 volts<br> <span class="fs-3 fw-light">180
                            Amps</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">7 x 4 x 7<br> <span
                            class="fs-3 fw-light">1100kg</span></div>
                </div>
                <div class="row text-center mb-5">
                    <div class="col-md-3 col-sm-12 fw-bolder fs-1">TYP 250</div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">250KVa/200KW<br><span class="fs-3 fw-light">Three
                            Phase</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">480 volts<br> <span class="fs-3 fw-light">300
                            Amps</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">7 x 4 x 7<br> <span
                            class="fs-3 fw-light">1500kg</span></div>
                </div>
                <div class="row text-center mb-5">
                    <div class="col-md-3 col-sm-12 fw-bolder fs-1">TYP 620</div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">625KVa/500KW<br><span class="fs-3 fw-light">Three
                            Phase</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">480 volts<br> <span class="fs-3 fw-light">600
                            Amps</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">10 x 4 7<br> <span class="fs-3 fw-light">3000kg</span>
                    </div>
                </div>
                <div class="row text-center mb-5">
                    <div class="col-md-3 col-sm-12 fw-bolder fs-1">TYP 1000</div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">1000KVa/800KW<br><span class="fs-3 fw-light">Three
                            Phase</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1 ">480 volts<br> <span class="fs-3 fw-light">1200
                            Amps</span></div>
                    <div class="col-md-3 col-sm-12 fw-light fs-1">12 x 5 10<br> <span
                            class="fs-3 fw-light">5000kg</span></div>
                </div>
                <div class="text-center">
                    <a href="./order.html" class="btn btn-primary p2 text-center my-4">Click Here To Place Order!</a>
                </div>
            </div>
        </section>

        <!-- clients -->

        <section id="clients" class="py-5">
            <div class="container">
                <h1 class="text-center">Our Clients</h1>
                <div class="row text-center">
                    <div class="col-lg-3 col-md-4 col-xs-6">
                        <div class="client-logo my-3">
                            <img src="./assets/images/client-1.png" alt="client-logo" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-xs-6">
                        <div class="client-logo my-3">
                            <img src="./assets/images/client-2.png" alt="client-logo" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-xs-6">
                        <div class="client-logo my-3">
                            <img src="./assets/images/client-3.png" alt="client-logo" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-xs-6">
                        <div class="client-logo my-3">
                            <img src="./assets/images/client-4.png" alt="client-logo" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-xs-6">
                        <div class="client-logo my-3">
                            <img src="./assets/images/client-5.png" alt="client-logo" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-xs-6">
                        <div class="client-logo my-3">
                            <img src="./assets/images/client-6.png" alt="client-logo" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-xs-6">
                        <div class="client-logo my-3">
                            <img src="./assets/images/client-7.png" alt="client-logo" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-xs-6">
                        <div class="client-logo my-3">
                            <img src="./assets/images/client-8.png" alt="client-logo" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- contact section -->
        <section class="contact" id="contact">
            <h1 class="fw-1 h1 py-3">Contact Us</h1>
            <div class="row">
                <div class="col-lg">
                    <div class="contact-content">
                        <h3 class="h2">Need Help? Don't Forget to Contact Us</h3>
                        <p>We will ensure that all your questions are answered and your needs are met cordially.
                            We assure you that our product produces good quality electricity and last very long.
                        </p>
                        <div class="icon">
                            <i class="fas fa-envelope"></i>
                            <p>admin@windelectric.com.ng</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-phone"></i>
                            <p>+234 9074719592 <br> +2347030422454</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-globe"></i>
                            <p>www.Wind Electric.com.ng</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg">
                    <form method="post" id="form_contact">
                        <div>
                            <input name="name" class="contact-input" type="text" placeholder="Name" id="inp_name"
                                octavalidate="R,ALPHA_SPACES">
                        </div>
                        <div>
                            <input name="email" class="contact-input" type="email" placeholder="Your Email"
                                id="inp_email" octavalidate="R,EMAIL">
                        </div>
                        <div>
                            <input name="phone" class="contact-input" type="number" placeholder="Your Number"
                                id="inp_num" octavalidate="R,DIGITS">
                        </div>
                        <div>
                            <textarea name="message" id="inp_message" cols="30" rows="10" placeholder="Message"
                                octavalidate="R,TEXT"></textarea>
                        </div>
                        <input type="submit" class="btn" value="Send Message">
                    </form>
                </div>
            </div>
        </section>

        <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
        <script src="./main.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
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
            $('#form_contact').on('submit', (e) => {
                const f = new octaValidate(e.target.id);
                if (!f.validate()) {
                    e.preventDefault();
                } else {
                    e.currentTarget.submit()
                }
            })
        </script>
</body>

</html>