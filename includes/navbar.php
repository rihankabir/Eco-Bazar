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
<nav class="navbar navbar-expand-lg ">

    <div class="container">
        
        <a class="navbar-brand" href="/Ecomart/">
            <?= e(APP_NAME); ?>
        </a>

        

         <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>

            <div class="d-flex align-items-center gap-3">
                <a href="/Ecomart/account/index.php?tab=wishlist"><i class="bi bi-heart wishlist-icon"></i></a>

                <div class="LINE"></div>
                <a
                    href="/Ecomart/cart.php"
                    class="text-decoration-none"
                >
                    <img src="/Ecomart/assets/images/icons/Bag.png"class="position-relative cart-image">
                    <span class="badge rounded-pill bg-success cart-count-icon position-absolute top-10 start-10">
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