<!--Topnav -->
<div class="container">
    <div class="row">
        <div class="top-nav-content d-flex justify-content-between align-items-center">
            <p class="topnavtext">Store Location: Lincoln- 344, Illinois, Chicago, USA</p>
            <div class="dmenu d-flex justify-content-center align-items-center">
            <div class="dropdown">
  <button class="btn  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
   Eng
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Eng</a></li>
    <li><a class="dropdown-item" href="#">Fra</a></li>
    <li><a class="dropdown-item" href="#">Ita</a></li>
  </ul>
</div>
 <div class="dropdown">
  <button class="btn  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
   USD
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">USD</a></li>
    <li><a class="dropdown-item" href="#">GBP</a></li>
    <li><a class="dropdown-item" href="#">EUR</a></li>
  </ul>
</div>
<div class="LINE"></div>
  <a
                    href="/Ecomart/auth/login.php"
                    class="text-decoration-none text-black me-1"
                >
                    Sign In
                </a>/
                <a
                    href="/Ecomart/auth/register.php"
                    class="text-decoration-none text-black ms-1"
                >
                    Sign Up
                </a>
            </div>
        </div>
    </div>
</div>
<!--Topnav ends -->
<nav class="navbar navbar-expand-lg main-navbar">

    <div class="container">
        
        <a class="navbar-brand" href="/Ecomart/">
            
            <img src="/Ecomart/assets/images/logo/ecobazar-logo.png">
        </a>

        

      <form class="d-flex search-form" role="search">

    <div class="search-input-wrapper position-relative">

        <i class="bi bi-search search-icon"></i>

        <input
            class="form-control"
            type="search"
            placeholder="Search for products..."
            aria-label="Search"
        >

    </div>

    <button class="btn search-btn" type="submit">
        Search
    </button>

</form>
            <div class="d-flex align-items-center gap-3">
                <a href="/Ecomart/account/index.php?tab=wishlist"><i class="bi bi-heart wishlist-icon"></i></a>

                <div class="LINE"></div>
                <a
                    href="/Ecomart/cart.php"
                    class="text-decoration-none"
                >
                    <img src="/Ecomart/assets/images/icons/Bag.png"class="position-relative cart-image">
                    <span class="badge rounded-pill bg-success cart-count-icon position-absolute top-10 start-10"id="cartCount">
                        0
                    </span>

                </a>

              <div class="shopping-text-count  justify-content-center align-items-center">
                <p class="cart-text p-0 m-0">Shopping cart:</p>
                <p class="cart-number p-0 m-0 text-bold text-dark">$57.00</p>
              </div>

            </div>

        </div>

    </div>



</nav>
<!-- Bottom Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark bottom-nav">

    <div class="container">

        <!-- Toggle Button -->
        <button
            class="navbar-toggler ms-auto"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#bottomNavbar"
            aria-controls="bottomNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Left Navigation -->
        <div class="collapse navbar-collapse bg-dark" id="bottomNavbar">

            <ul class="navbar-nav">

                <!-- Dropdown 1 -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Home
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Home 1</a></li>
                        <li><a class="dropdown-item" href="#">Home 2</a></li>
                    </ul>
                </li>

                <!-- Dropdown 2 -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Shop
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Shop</a></li>
                        <li><a class="dropdown-item" href="#">Shop Details</a></li>
                    </ul>
                </li>

                <!-- Dropdown 3 -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Pages
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">About</a></li>
                        <li><a class="dropdown-item" href="#">Contact Us</a></li>
                    </ul>
                </li>

                <!-- Dropdown 4 -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Blog
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Blog</a></li>
                        <li><a class="dropdown-item" href="#">Blog Details</a></li>
                    </ul>
                </li>

                <!-- Normal Page 1 -->
                <li class="nav-item">
                    <a class="nav-link" href="/Ecomart/about.php">
                        About Us
                    </a>
                </li>

                <!-- Normal Page 2 -->
                <li class="nav-item">
                    <a class="nav-link" href="/Ecomart/contact.php">
                        Contact
                    </a>
                </li>

            </ul>

        </div>

        <!-- Right Side Phone -->
        <a href="tel:+1234567890" class="phone-number text-white text-decoration-none">
            <i class="bi bi-telephone"></i>
            <span>(219) 555-0114</span>
        </a>

    </div>

</nav>
<!-- Bottom Navigation Ends -->