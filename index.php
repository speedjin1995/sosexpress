<?php
require_once 'php/db_connect.php';

$blog = $db->query("SELECT * FROM blog WHERE deleted = '0'");
$id = '1';
$stmt = $db->prepare("SELECT * from companies where id = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
$result = $stmt->get_result();
$name = '';
$address = '';
$phone = '';
$email = '';
$description  = '';

if(($row = $result->fetch_assoc()) !== null){
  $name = $row['name'];
  $address = $row['address'];
  $phone = $row['phone'];
  $email = $row['email'];
  $description = $row['description'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!--====== Required meta tags ======-->
  <meta charset="utf-8" />
  <meta http-equiv="x-ua-compatible" content="ie=edge" />
  <meta name="description" content="" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <!--====== Title ======-->
  <title>Pengangkutan SOS</title>

  <!--====== Favicon Icon ======-->
  <link rel="icon" href="assets/logo.png" type="image">

  <!--====== Bootstrap css ======-->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />

  <!--====== Line Icons css ======-->
  <link rel="stylesheet" href="assets/css/lineicons.css" />

  <!--====== Tiny Slider css ======-->
  <link rel="stylesheet" href="assets/css/tiny-slider.css" />

  <!--====== gLightBox css ======-->
  <link rel="stylesheet" href="assets/css/glightbox.min.css" />

  <link rel="stylesheet" href="style.css" />
</head>

<body>

  <!--====== NAVBAR NINE PART START ======-->

  <section class="navbar-area navbar-nine">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <nav class="navbar navbar-expand-lg">
            <a class="navbar-brand" href="index.html">
              <img src="assets/logo.png" alt="Logo" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNine"
              aria-controls="navbarNine" aria-expanded="false" aria-label="Toggle navigation">
              <span class="toggler-icon"></span>
              <span class="toggler-icon"></span>
              <span class="toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse sub-menu-bar" id="navbarNine">
              <ul class="navbar-nav me-auto">
                <li class="nav-item">
                  <a class="page-scroll active" href="#hero-area">Home</a>
                </li>
                <li class="nav-item">
                  <a class="page-scroll" href="#blog">Latest News</a>
                </li>
                <!--li class="nav-item">
                  <a class="page-scroll" href="#services">Services</a>
                </li-->
                <!--li class="nav-item">
                  <a class="page-scroll" href="#pricing">Pricing</a>
                </li-->
                <li class="nav-item">
                  <a class="page-scroll" href="#contact">Contact</a>
                </li>
              </ul>
            </div>

            <div class="navbar-btn d-none d-lg-inline-block">
              <a class="menu-bar" href="#side-menu-left"><i class="lni lni-menu"></i></a>
            </div>
          </nav>
          <!-- navbar -->
        </div>
      </div>
      <!-- row -->
    </div>
    <!-- container -->
  </section>

  <!--====== NAVBAR NINE PART ENDS ======-->

  <!--====== SIDEBAR PART START ======-->

  <div class="sidebar-left">
    <div class="sidebar-close">
      <a class="close" href="#close"><i class="lni lni-close"></i></a>
    </div>
    <div class="sidebar-content">
      <div class="sidebar-logo">
        <a href="index.html"><img src="assets/logo.png" alt="Logo" /></a>
      </div>
      <p class="text">Call S.O.S today. We are ready to provide optimal service.</p>
      <!-- logo -->
      <div class="sidebar-menu">
        <h5 class="menu-title">Quick Links</h5>
        <ul>
          <li><a href="#blog">Latest News</a></li>
          <li><a href="#contact">Contact Us</a></li>
          <li><a href="customers/index.php">Login</a></li>
        </ul>
      </div>
      <!-- menu -->
      <!--div class="sidebar-social align-items-center justify-content-center">
        <h5 class="social-title">Follow Us On</h5>
        <ul>
          <li>
            <a href="javascript:void(0)"><i class="lni lni-facebook-filled"></i></a>
          </li>
          <li>
            <a href="javascript:void(0)"><i class="lni lni-twitter-original"></i></a>
          </li>
          <li>
            <a href="javascript:void(0)"><i class="lni lni-linkedin-original"></i></a>
          </li>
          <li>
            <a href="javascript:void(0)"><i class="lni lni-youtube"></i></a>
          </li>
        </ul>
      </div-->
      <!-- sidebar social -->
    </div>
    <!-- content -->
  </div>
  <div class="overlay-left"></div>

  <!--====== SIDEBAR PART ENDS ======-->

  <!-- Start header Area -->
  <section id="hero-area" class="header-area header-eight">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-12 col-12">
          <div class="header-content">
            <h1>Call S.O.S today. We are ready to provide optimal service.</h1>
            <p>
              S.O.S is a huge and professional freight company in Peninsular Malaysia. The company is an expert in providing freight services to every hypermarket in the country. In this highly competitive freight industry, S.O.S places high importance on our clients as our first ambition in order to maintain a long-term business relationship with our partners, and this has makes the business expand rapidly.
            </p>
            <p>
            Although S.O.S provides high quality freight services in the Peninsular Malaysia, we move forward to extend the business by improving and strengthening our management on the procedure of freightage. Furthermore, we will maximize our abilities to fulfill different partners' satisfaction.
            </p>
            <!--div class="button">
              <a href="javascript:void(0)" class="btn primary-btn">Get Started</a>
              <a href="https://www.youtube.com/watch?v=r44RKWyfcFw&fbclid=IwAR21beSJORalzmzokxDRcGfkZA1AtRTE__l5N4r09HcGS5Y6vOluyouM9EM"
                class="glightbox video-button">
                <span class="btn icon-btn rounded-full">
                  <i class="lni lni-play"></i>
                </span>
                <span class="text">Watch Intro</span>
              </a>
            </div-->
          </div>
        </div>
        <!--div class="col-lg-6 col-md-12 col-12">
          <div class="header-image">
            <img src="assets/images/header/hero-image.jpg" alt="#" />
          </div>
        </div-->
      </div>
    </div>
  </section>
  <!-- End header Area -->

  <!--====== ABOUT FIVE PART START ======-->
  <!--section class="about-area about-five">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 col-12">
          <div class="about-image-five">
            <svg class="shape" width="106" height="134" viewBox="0 0 106 134" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <circle cx="1.66654" cy="1.66679" r="1.66667" fill="#DADADA" />
              <circle cx="1.66654" cy="16.3335" r="1.66667" fill="#DADADA" />
              <circle cx="1.66654" cy="31.0001" r="1.66667" fill="#DADADA" />
              <circle cx="1.66654" cy="45.6668" r="1.66667" fill="#DADADA" />
              <circle cx="1.66654" cy="60.3335" r="1.66667" fill="#DADADA" />
              <circle cx="1.66654" cy="88.6668" r="1.66667" fill="#DADADA" />
              <circle cx="1.66654" cy="117.667" r="1.66667" fill="#DADADA" />
              <circle cx="1.66654" cy="74.6668" r="1.66667" fill="#DADADA" />
              <circle cx="1.66654" cy="103" r="1.66667" fill="#DADADA" />
              <circle cx="1.66654" cy="132" r="1.66667" fill="#DADADA" />
              <circle cx="16.3333" cy="1.66679" r="1.66667" fill="#DADADA" />
              <circle cx="16.3333" cy="16.3335" r="1.66667" fill="#DADADA" />
              <circle cx="16.3333" cy="31.0001" r="1.66667" fill="#DADADA" />
              <circle cx="16.3333" cy="45.6668" r="1.66667" fill="#DADADA" />
              <circle cx="16.333" cy="60.3335" r="1.66667" fill="#DADADA" />
              <circle cx="16.333" cy="88.6668" r="1.66667" fill="#DADADA" />
              <circle cx="16.333" cy="117.667" r="1.66667" fill="#DADADA" />
              <circle cx="16.333" cy="74.6668" r="1.66667" fill="#DADADA" />
              <circle cx="16.333" cy="103" r="1.66667" fill="#DADADA" />
              <circle cx="16.333" cy="132" r="1.66667" fill="#DADADA" />
              <circle cx="30.9998" cy="1.66679" r="1.66667" fill="#DADADA" />
              <circle cx="74.6665" cy="1.66679" r="1.66667" fill="#DADADA" />
              <circle cx="30.9998" cy="16.3335" r="1.66667" fill="#DADADA" />
              <circle cx="74.6665" cy="16.3335" r="1.66667" fill="#DADADA" />
              <circle cx="30.9998" cy="31.0001" r="1.66667" fill="#DADADA" />
              <circle cx="74.6665" cy="31.0001" r="1.66667" fill="#DADADA" />
              <circle cx="30.9998" cy="45.6668" r="1.66667" fill="#DADADA" />
              <circle cx="74.6665" cy="45.6668" r="1.66667" fill="#DADADA" />
              <circle cx="31" cy="60.3335" r="1.66667" fill="#DADADA" />
              <circle cx="74.6668" cy="60.3335" r="1.66667" fill="#DADADA" />
              <circle cx="31" cy="88.6668" r="1.66667" fill="#DADADA" />
              <circle cx="74.6668" cy="88.6668" r="1.66667" fill="#DADADA" />
              <circle cx="31" cy="117.667" r="1.66667" fill="#DADADA" />
              <circle cx="74.6668" cy="117.667" r="1.66667" fill="#DADADA" />
              <circle cx="31" cy="74.6668" r="1.66667" fill="#DADADA" />
              <circle cx="74.6668" cy="74.6668" r="1.66667" fill="#DADADA" />
              <circle cx="31" cy="103" r="1.66667" fill="#DADADA" />
              <circle cx="74.6668" cy="103" r="1.66667" fill="#DADADA" />
              <circle cx="31" cy="132" r="1.66667" fill="#DADADA" />
              <circle cx="74.6668" cy="132" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="1.66679" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="1.66679" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="16.3335" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="16.3335" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="31.0001" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="31.0001" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="45.6668" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="45.6668" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="60.3335" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="60.3335" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="88.6668" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="88.6668" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="117.667" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="117.667" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="74.6668" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="74.6668" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="103" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="103" r="1.66667" fill="#DADADA" />
              <circle cx="45.6665" cy="132" r="1.66667" fill="#DADADA" />
              <circle cx="89.3333" cy="132" r="1.66667" fill="#DADADA" />
              <circle cx="60.3333" cy="1.66679" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="1.66679" r="1.66667" fill="#DADADA" />
              <circle cx="60.3333" cy="16.3335" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="16.3335" r="1.66667" fill="#DADADA" />
              <circle cx="60.3333" cy="31.0001" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="31.0001" r="1.66667" fill="#DADADA" />
              <circle cx="60.3333" cy="45.6668" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="45.6668" r="1.66667" fill="#DADADA" />
              <circle cx="60.333" cy="60.3335" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="60.3335" r="1.66667" fill="#DADADA" />
              <circle cx="60.333" cy="88.6668" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="88.6668" r="1.66667" fill="#DADADA" />
              <circle cx="60.333" cy="117.667" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="117.667" r="1.66667" fill="#DADADA" />
              <circle cx="60.333" cy="74.6668" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="74.6668" r="1.66667" fill="#DADADA" />
              <circle cx="60.333" cy="103" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="103" r="1.66667" fill="#DADADA" />
              <circle cx="60.333" cy="132" r="1.66667" fill="#DADADA" />
              <circle cx="104" cy="132" r="1.66667" fill="#DADADA" />
            </svg>
            <img src="assets/images/about/about-img1.jpg" alt="about" />
          </div>
        </div>
        <div class="col-lg-6 col-12">
          <div class="about-five-content">
            <h6 class="small-title text-lg">OUR STORY</h6>
            <h2 class="main-title fw-bold">Our team comes with the experience and knowledge</h2>
            <div class="about-five-tab">
              <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <button class="nav-link active" id="nav-who-tab" data-bs-toggle="tab" data-bs-target="#nav-who"
                    type="button" role="tab" aria-controls="nav-who" aria-selected="true">Who We Are</button>
                  <button class="nav-link" id="nav-vision-tab" data-bs-toggle="tab" data-bs-target="#nav-vision"
                    type="button" role="tab" aria-controls="nav-vision" aria-selected="false">our Vision</button>
                  <button class="nav-link" id="nav-history-tab" data-bs-toggle="tab" data-bs-target="#nav-history"
                    type="button" role="tab" aria-controls="nav-history" aria-selected="false">our History</button>
                </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-who" role="tabpanel" aria-labelledby="nav-who-tab">
                  <p>It is a long established fact that a reader will be distracted by the readable content of a page
                    when
                    looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
                    distribution of letters, look like readable English.</p>
                  <p>There are many variations of passages of Lorem Ipsum available, but the majority have in some
                    form,
                    by injected humour.</p>
                </div>
                <div class="tab-pane fade" id="nav-vision" role="tabpanel" aria-labelledby="nav-vision-tab">
                  <p>It is a long established fact that a reader will be distracted by the readable content of a page
                    when
                    looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
                    distribution of letters, look like readable English.</p>
                  <p>There are many variations of passages of Lorem Ipsum available, but the majority have in some
                    form,
                    by injected humour.</p>
                </div>
                <div class="tab-pane fade" id="nav-history" role="tabpanel" aria-labelledby="nav-history-tab">
                  <p>It is a long established fact that a reader will be distracted by the readable content of a page
                    when
                    looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
                    distribution of letters, look like readable English.</p>
                  <p>There are many variations of passages of Lorem Ipsum available, but the majority have in some
                    form,
                    by injected humour.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div><!-- container -->
  <!--/section-->

  <!--====== ABOUT FIVE PART ENDS ======-->

  <!-- ===== service-area start ===== -->
  <!--section id="services" class="services-area services-eight">
    <div class="section-title-five">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="content">
              <h6>Services</h6>
              <h2 class="fw-bold">Our Best Services</h2>
              <p>
                There are many variations of passages of Lorem Ipsum available,
                but the majority have suffered alteration in some form.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-lg-4 col-md-6">
          <div class="single-services">
            <div class="service-icon">
              <i class="lni lni-capsule"></i>
            </div>
            <div class="service-content">
              <h4>Refreshing Design</h4>
              <p>
                Lorem ipsum dolor sit amet, adipscing elitr, sed diam nonumy
                eirmod tempor ividunt labor dolore magna.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="single-services">
            <div class="service-icon">
              <i class="lni lni-bootstrap"></i>
            </div>
            <div class="service-content">
              <h4>Solid Bootstrap 5</h4>
              <p>
                Lorem ipsum dolor sit amet, adipscing elitr, sed diam nonumy
                eirmod tempor ividunt labor dolore magna.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="single-services">
            <div class="service-icon">
              <i class="lni lni-shortcode"></i>
            </div>
            <div class="service-content">
              <h4>100+ Components</h4>
              <p>
                Lorem ipsum dolor sit amet, adipscing elitr, sed diam nonumy
                eirmod tempor ividunt labor dolore magna.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="single-services">
            <div class="service-icon">
              <i class="lni lni-dashboard"></i>
            </div>
            <div class="service-content">
              <h4>Speed Optimized</h4>
              <p>
                Lorem ipsum dolor sit amet, adipscing elitr, sed diam nonumy
                eirmod tempor ividunt labor dolore magna.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="single-services">
            <div class="service-icon">
              <i class="lni lni-layers"></i>
            </div>
            <div class="service-content">
              <h4>Fully Customizable</h4>
              <p>
                Lorem ipsum dolor sit amet, adipscing elitr, sed diam nonumy
                eirmod tempor ividunt labor dolore magna.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="single-services">
            <div class="service-icon">
              <i class="lni lni-reload"></i>
            </div>
            <div class="service-content">
              <h4>Regular Updates</h4>
              <p>
                Lorem ipsum dolor sit amet, adipscing elitr, sed diam nonumy
                eirmod tempor ividunt labor dolore magna.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section-->
  <!-- ===== service-area end ===== -->


  <!-- Start Pricing  Area -->
  <!--section id="pricing" class="pricing-area pricing-fourteen">
    <div class="section-title-five">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="content">
              <h6>Pricing</h6>
              <h2 class="fw-bold">Pricing & Plans</h2>
              <p>
                There are many variations of passages of Lorem Ipsum available,
                but the majority have suffered alteration in some form.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-lg-4 col-md-6 col-12">
          <div class="pricing-style-fourteen">
            <div class="table-head">
              <h6 class="title">Starter</h4>
                <p>Lorem Ipsum is simply dummy text of the printing and industry.</p>
                <div class="price">
                  <h2 class="amount">
                    <span class="currency">$</span>0<span class="duration">/mo </span>
                  </h2>
                </div>
            </div>

            <div class="light-rounded-buttons">
              <a href="javascript:void(0)" class="btn primary-btn-outline">
                Start free trial
              </a>
            </div>

            <div class="table-content">
              <ul class="table-list">
                <li> <i class="lni lni-checkmark-circle"></i> Cras justo odio.</li>
                <li> <i class="lni lni-checkmark-circle"></i> Dapibus ac facilisis in.</li>
                <li> <i class="lni lni-checkmark-circle deactive"></i> Morbi leo risus.</li>
                <li> <i class="lni lni-checkmark-circle deactive"></i> Excepteur sint occaecat velit.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12">
          <div class="pricing-style-fourteen middle">
            <div class="table-head">
              <h6 class="title">Exclusive</h4>
                <p>Lorem Ipsum is simply dummy text of the printing and industry.</p>
                <div class="price">
                  <h2 class="amount">
                    <span class="currency">$</span>99<span class="duration">/mo </span>
                  </h2>
                </div>
            </div>

            <div class="light-rounded-buttons">
              <a href="javascript:void(0)" class="btn primary-btn">
                Start free trial
              </a>
            </div>

            <div class="table-content">
              <ul class="table-list">
                <li> <i class="lni lni-checkmark-circle"></i> Cras justo odio.</li>
                <li> <i class="lni lni-checkmark-circle"></i> Dapibus ac facilisis in.</li>
                <li> <i class="lni lni-checkmark-circle"></i> Morbi leo risus.</li>
                <li> <i class="lni lni-checkmark-circle deactive"></i> Excepteur sint occaecat velit.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12">
          <div class="pricing-style-fourteen">
            <div class="table-head">
              <h6 class="title">Premium</h4>
                <p>Lorem Ipsum is simply dummy text of the printing and industry.</p>
                <div class="price">
                  <h2 class="amount">
                    <span class="currency">$</span>150<span class="duration">/mo </span>
                  </h2>
                </div>
            </div>

            <div class="light-rounded-buttons">
              <a href="javascript:void(0)" class="btn primary-btn-outline">
                Start free trial
              </a>
            </div>

            <div class="table-content">
              <ul class="table-list">
                <li> <i class="lni lni-checkmark-circle"></i> Cras justo odio.</li>
                <li> <i class="lni lni-checkmark-circle"></i> Dapibus ac facilisis in.</li>
                <li> <i class="lni lni-checkmark-circle"></i> Morbi leo risus.</li>
                <li> <i class="lni lni-checkmark-circle"></i> Excepteur sint occaecat velit.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section-->
  <!--/ End Pricing  Area -->

  <!-- Start Cta Area -->
  <!--section id="call-action" class="call-action">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-xxl-6 col-xl-7 col-lg-8 col-md-9">
          <div class="inner-content">
            <h2>We love to make perfect <br />solutions for your business</h2>
            <p>
              Why I say old chap that is, spiffing off his nut cor blimey
              guvnords geeza<br />
              bloke knees up bobby, sloshed arse William cack Richard. Bloke
              fanny around chesed of bum bag old lost the pilot say there
              spiffing off his nut.
            </p>
            <div class="light-rounded-buttons">
              <a href="javascript:void(0)" class="btn primary-btn-outline">Get Started</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section-->
  <!-- End Cta Area -->

  <!-- Start Latest News Area -->
  <div id="blog" class="latest-news-area section">
    <!--======  Start Section Title Five ======-->
    <div class="section-title-five">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="content">
              <h6>latest news</h6>
              <h2 class="fw-bold">Latest News</h2>
              <p>
                All latest update and news
              </p>
            </div>
          </div>
        </div>
        <!-- row -->
      </div>
      <!-- container -->
    </div>
    <!--======  End Section Title Five ======-->
    <div class="container">
      <div class="row">
      <?php while($row = mysqli_fetch_assoc($blog)): ?>
        <div class="col-lg-4 col-md-6 col-12">
          <!-- Single News -->
          <div class="single-news">
            <div class="content-body">
              <h4 class="title">
                <a href="javascript:void(0)"><?= $row['title_en'] ?> </a>
              </h4>
              <p><?= $row['content_en'] ?></p>
            </div>
          </div>
          <!-- End Single News -->
        </div>
      <?php endwhile; ?>
      </div>
    </div>
  </div>
  <!-- End Latest News Area -->

  <!-- Start Brand Area -->
  <!--div id="clients" class="brand-area section">
    <div class="section-title-five">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="content">
              <h6>Meet our Clients</h6>
              <h2 class="fw-bold">Our Awesome Clients</h2>
              <p>
                There are many variations of passages of Lorem Ipsum available,
                but the majority have suffered alteration in some form.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 offset-lg-2 col-12">
          <div class="clients-logos">
            <div class="single-image">
              <img src="assets/images/client-logo/graygrids.svg" alt="Brand Logo Images" />
            </div>
            <div class="single-image">
              <img src="assets/images/client-logo/uideck.svg" alt="Brand Logo Images" />
            </div>
            <div class="single-image">
              <img src="assets/images/client-logo/ayroui.svg" alt="Brand Logo Images" />
            </div>
            <div class="single-image">
              <img src="assets/images/client-logo/lineicons.svg" alt="Brand Logo Images" />
            </div>
            <div class="single-image">
              <img src="assets/images/client-logo/tailwindtemplates.svg" alt="Brand Logo Images" />
            </div>
            <div class="single-image">
              <img src="assets/images/client-logo/ecomhtml.svg" alt="Brand Logo Images" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div-->
  <!-- End Brand Area -->

  <!-- ========================= contact-section start ========================= -->
  <section id="contact" class="contact-section">
    <div class="container">
      <div class="row">
        <div class="col-xl-4">
          <div class="contact-item-wrapper">
            <div class="row">
              <div class="col-12 col-md-6 col-xl-12">
                <div class="contact-item">
                  <div class="contact-icon">
                    <i class="lni lni-phone"></i>
                  </div>
                  <div class="contact-content">
                    <h4>Contact</h4>
                    <p><?=$phone ?></p>
                    <p><?=$email ?></p>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-xl-12">
                <div class="contact-item">
                  <div class="contact-icon">
                    <i class="lni lni-map-marker"></i>
                  </div>
                  <div class="contact-content">
                    <h4>Address</h4>
                    <p><?=$address ?></p>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-xl-12">
                <div class="contact-item">
                  <div class="contact-icon">
                    <i class="lni lni-alarm-clock"></i>
                  </div>
                  <div class="contact-content">
                    <h4>Schedule</h4>
                    <p><?=$description ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--div class="col-xl-8">
          <div class="contact-form-wrapper">
            <div class="row">
              <div class="col-xl-10 col-lg-8 mx-auto">
                <div class="section-title text-center">
                  <span> Get in Touch </span>
                  <h2>
                    Ready to Get Started
                  </h2>
                  <p>
                    At vero eos et accusamus et iusto odio dignissimos ducimus
                    quiblanditiis praesentium
                  </p>
                </div>
              </div>
            </div>
            <form action="#" class="contact-form">
              <div class="row">
                <div class="col-md-6">
                  <input type="text" name="name" id="name" placeholder="Name" required />
                </div>
                <div class="col-md-6">
                  <input type="email" name="email" id="email" placeholder="Email" required />
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <input type="text" name="phone" id="phone" placeholder="Phone" required />
                </div>
                <div class="col-md-6">
                  <input type="text" name="subject" id="email" placeholder="Subject" required />
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <textarea name="message" id="message" placeholder="Type Message" rows="5"></textarea>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="button text-center rounded-buttons">
                    <button type="submit" class="btn primary-btn rounded-full">
                      Send Message
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div-->
      </div>
    </div>
  </section>
  <!-- ========================= contact-section end ========================= -->

  <!-- ========================= map-section end ========================= -->
  <section class="map-section map-style-9">
    <div class="map-container">
      <object style="border:0; height: 500px; width: 100%;"
        data="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15933.930113016866!2d101.6879635!3d3.229379!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc47759131d6c5%3A0x4b04467468c70948!2sPengangkutan%20S.O.S%20Sdn%20Bhd!5e0!3m2!1szh-CN!2smy!4v1706782818176!5m2!1szh-CN!2smy"></object>
    </div>
    </div>
  </section>
  <!-- ========================= map-section end ========================= -->

  <!-- Start Footer Area -->
  <footer class="footer-area footer-eleven">
    <!-- Start Footer Top -->
    <div class="footer-top">
      <div class="container">
        <div class="inner-content">
          <div class="row">
            <div class="col-lg-4 col-md-6 col-12">
              <!-- Single Widget -->
              <div class="footer-widget f-about">
                <div class="logo">
                  <a href="index.html">
                    <img src="assets/logo.png" alt="#" class="img-fluid" />
                  </a>
                </div>
              </div>
              <!-- End Single Widget -->
            </div>
            <div class="col-lg-4 col-md-6 col-12">
              <!-- Single Widget -->
              <div class="footer-widget f-link">
                <p>Call S.O.S today. We are ready to provide optimal service.</p>
              </div>
              <!-- End Single Widget -->
            </div>
            <div class="col-lg-4 col-md-6 col-12">
              <!-- Single Widget -->
              <div class="footer-widget f-link">
                <p class="copyright-text">
                  <span>© 2024 </span>Developed by
                  <a href="javascript:void(0)" rel="nofollow"> PENGANGKUTAN S.O.S SDN. BHD.(714353-A) </a>
                </p>
              </div>
              <!-- End Single Widget -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ End Footer Top -->
  </footer>
  <!--/ End Footer Area -->

  <a href="#" class="scroll-top btn-hover">
    <i class="lni lni-chevron-up"></i>
  </a>

  <!--====== js ======-->
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/glightbox.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/tiny-slider.js"></script>

  <script>

    //===== close navbar-collapse when a  clicked
    let navbarTogglerNine = document.querySelector(
      ".navbar-nine .navbar-toggler"
    );
    navbarTogglerNine.addEventListener("click", function () {
      navbarTogglerNine.classList.toggle("active");
    });

    // ==== left sidebar toggle
    let sidebarLeft = document.querySelector(".sidebar-left");
    let overlayLeft = document.querySelector(".overlay-left");
    let sidebarClose = document.querySelector(".sidebar-close .close");

    overlayLeft.addEventListener("click", function () {
      sidebarLeft.classList.toggle("open");
      overlayLeft.classList.toggle("open");
    });
    sidebarClose.addEventListener("click", function () {
      sidebarLeft.classList.remove("open");
      overlayLeft.classList.remove("open");
    });

    // ===== navbar nine sideMenu
    let sideMenuLeftNine = document.querySelector(".navbar-nine .menu-bar");

    sideMenuLeftNine.addEventListener("click", function () {
      sidebarLeft.classList.add("open");
      overlayLeft.classList.add("open");
    });

    //========= glightbox
    GLightbox({
      'href': 'https://www.youtube.com/watch?v=r44RKWyfcFw&fbclid=IwAR21beSJORalzmzokxDRcGfkZA1AtRTE__l5N4r09HcGS5Y6vOluyouM9EM',
      'type': 'video',
      'source': 'youtube', //vimeo, youtube or local
      'width': 900,
      'autoplayVideos': true,
    });

  </script>
</body>

</html>