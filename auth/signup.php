<?php
  include __DIR__ . '/../layout/headerLayout.php'
?>

<?php
    $role = $_GET['role'];
    if (!in_array($role, ['customer', 'restaurant', 'delivery'])) {
        header("Location: ../onboarding.php");
        exit();
    }
?>

<section class="a-nav">
    <div class="buttons is-link none">
        <button id="themeToggle" class="button is-light none">
            <span class="icon">
                <i class="fas fa-moon"></i>
            </span>
        </button>
    </div>
</section>
    
<section class="hero is-fullheight a">
  <div class="hero-body">
    <div class="container">
      <div class="columns is-centered">
        <div class="column is-5-tablet is-4-desktop is-3-widescreen">
          <div class="box has-shadow" style="border-top: 4px solid #ff7b25;">
            <div class="has-text-centered mb-5">
              <span class="icon-text">
                <span class="title is-3 has-text-weight-bold">Sweet Bite</span>
              </span>
              <p class="subtitle is-5 mt-2"><?php echo ucfirst($role) ?> Sign up</p>
            </div>
            
            <form action="./signup-pros.php" method="POST" onsubmit="return validateSignupForm()">
              <div class="field">
                <label class="label">Full Name</label>
                <div class="control has-icons-left">
                  <input class="input" type="text" name="fname" placeholder="e.g. John Doe" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-user"></i>
                  </span>
                </div>
              </div>

              <div class="field">
                <label class="label">Email</label>
                <div class="control has-icons-left">
                  <input class="input" type="email" name="email" placeholder="e.g. alex@example.com" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-envelope"></i>
                  </span>
                </div>
              </div>

              <div class="field">
                <label class="label">Password</label>
                <div class="control has-icons-left">
                  <input class="input" type="password" name="password" placeholder="********" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-lock"></i>
                  </span>
                </div>
              </div>

              <?php if ($role === 'restaurant'): ?>
              <div class="field">
                <label class="label">Restaurant Name</label>
                <div class="control has-icons-left">
                  <input class="input" type="text" name="restaurant_name" placeholder="e.g. Tasty Bites" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-store"></i>
                  </span>
                </div>
              </div>

              <div class="field">
                <label class="label">Phone Number</label>
                <div class="control has-icons-left">
                  <input class="input" type="tel" name="phone" placeholder="e.g. +1234567890" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-phone"></i>
                  </span>
                </div>
              </div>

              <div class="field">
                <label class="label">Address</label>
                <div class="control has-icons-left">
                  <textarea class="textarea" name="address" placeholder="Enter restaurant address" required></textarea>
                </div>
              </div>
              <?php endif; ?>

              <?php if ($role === 'delivery'): ?>
              <div class="field">
                <label class="label">Phone Number</label>
                <div class="control has-icons-left">
                  <input class="input" type="tel" name="phone" placeholder="e.g. +1234567890" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-phone"></i>
                  </span>
                </div>
              </div>

              <div class="field">
                <label class="label">Vehicle Type</label>
                <div class="control has-icons-left">
                  <div class="select is-fullwidth">
                    <select name="vehicle_type" required>
                      <option value="">Select vehicle type</option>
                      <option value="bicycle">Bicycle</option>
                      <option value="motorcycle">Motorcycle</option>
                      <option value="car">Car</option>
                    </select>
                  </div>
                </div>
              </div>
              <?php endif; ?>
              
              <div class="field">
                <button class="button is-primary is-fullwidth" type="submit">
                  <span class="icon">
                    <i class="fas fa-user-plus"></i>
                  </span>
                  <span>Sign Up</span>
                </button>
              </div>
              <input type="hidden" name="role" value="<?php echo $role ?>">
            </form>
            <div class="has-text-centered">
              <p class="is-size-7">Already have an account? <a href="./login.php">Login</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  function validateSignupForm() {
    const fname = document.querySelector('input[name="fname"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const password = document.querySelector('input[name="password"]').value;
    const role = document.querySelector('input[name="role"]').value;
    
    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    const namePattern = /^[A-Za-z\s]+$/;
    const phonePattern = /^\+?[\d\s-]{10,}$/;

    // Basic validation
    if (fname === "" || email === "" || password === "") {
      alert("All fields are required.");
      return false;
    }

    // Name validation
    if (!namePattern.test(fname)) {
      alert("Name can only contain letters and spaces.");
      return false;
    }
    if (fname.length < 3) {
      alert("Name must be at least 3 characters.");
      return false;
    }
    if (fname.length > 50) {
      alert("Name must be less than 50 characters.");
      return false;
    }

    // Email validation
    if (!emailPattern.test(email)) {
      alert("Please enter a valid email address.");
      return false;
    }
    if (email.length < 5 || email.length > 50) {
      alert("Email must be between 5 and 50 characters.");
      return false;
    }

    // Password validation
    if (password.length < 6) {
      alert("Password must be at least 6 characters.");
      return false;
    }
    if (password.length > 50) {
      alert("Password must be less than 50 characters.");
      return false;
    }

    // Role-specific validation
    if (role === 'restaurant') {
      const restaurantName = document.querySelector('input[name="restaurant_name"]').value.trim();
      const phone = document.querySelector('input[name="phone"]').value.trim();
      const address = document.querySelector('textarea[name="address"]').value.trim();

      if (!restaurantName || !phone || !address) {
        alert("All restaurant fields are required.");
        return false;
      }

      if (!phonePattern.test(phone)) {
        alert("Please enter a valid phone number.");
        return false;
      }
    }

    if (role === 'delivery') {
      const phone = document.querySelector('input[name="phone"]').value.trim();
      const vehicleType = document.querySelector('select[name="vehicle_type"]').value;

      if (!phone || !vehicleType) {
        alert("All delivery fields are required.");
        return false;
      }

      if (!phonePattern.test(phone)) {
        alert("Please enter a valid phone number.");
        return false;
      }
    }

    return true;
  }
</script>

<?php
  include __DIR__ . '/../layout/footerLayout.php'
?>
