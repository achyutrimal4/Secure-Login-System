let form = document.getElementById("form");
let username = document.getElementById("username");
let email = document.getElementById("email");
let password = document.getElementById("password");
let repassword = document.getElementById("re-password");
let currentPassword = document.getElementById("current-password");
let helper_characters = document.getElementById("helper-characters");



function validateEmail(email) {
  return /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
    email
  );
}

// toggle password visibility for first password
function passwordVisibility() {
  var inputPassword = document.getElementById("password");
  var y = document.getElementById("eye1");
  var z = document.getElementById("eye2");

  if (inputPassword.type === "password") {
    inputPassword.type = "text";
    y.style.display = "block";
    z.style.display = "none";
  } else {
    inputPassword.type = "password";
    y.style.display = "none";
    z.style.display = "block";
  }
}
// toggle password visibility- confirm password
function rePasswordVisibility() {
  var rePassword = document.getElementById("re-password");
  var y = document.getElementById("eye3");
  var z = document.getElementById("eye4");

  if (rePassword.type === "password") {
    rePassword.type = "text";
    y.style.display = "block";
    z.style.display = "none";
  } else {
    rePassword.type = "password";
    y.style.display = "none";
    z.style.display = "block";
  }
}
// toggle password visibility- current password in change password
function currentPassVis() {
  var currentPassword = document.getElementById("current-password");
  var y = document.getElementById("eye5");
  var z = document.getElementById("eye6");

  if (currentPassword.type === "password") {
    currentPassword.type = "text";
    y.style.display = "block";
    z.style.display = "none";
  } else {
    currentPassword.type = "password";
    y.style.display = "none";
    z.style.display = "block";
  }
}
// toggle password visibility- signin Password
function signinPasswordVisibility() {
  var currentPassword = document.getElementById("signin_password");
  var y = document.getElementById("eye-signin");
  var z = document.getElementById("eye-signin2");

  if (currentPassword.type === "password") {
    currentPassword.type = "text";
    y.style.display = "block";
    z.style.display = "none";
  } else {
    currentPassword.type = "password";
    y.style.display = "none";
    z.style.display = "block";
  }
}

// show and hide password helper text for 1st password input
function showHelper() {
  var helperText = document.querySelector(".helper-text");
  helperText.style.display = "block";
}
function hideHelper() {
  var helperText = document.querySelector(".helper-text");
  helperText.style.display = "none";
}
password.addEventListener("blur", () => {
  hideHelper();
});

// change colors in password helpers
function characterNumCheck() {
  if (document.getElementById("password").value.length >= 8) {
    document.getElementById("character-num").style.color = "#00bfa6";
  } else {
    document.getElementById("helper-characters").style.color = "black";
  }
}
//manipulate helper password feedback criteria using regex
function passwordStrength(password) {
  let s = 0;
  check1 = document.getElementById("check1");
  check2 = document.getElementById("check2");
  check3 = document.getElementById("check3");
  check4 = document.getElementById("check4");
  check5 = document.getElementById("check5");
  if (password.length > 6) {
    s++;
  }
  if (password.length >= 8) {
    s++;
    check1.style.visibility = "visible";
  }else{
    check1.style.visibility = "hidden";
  }
  if (/[A-Z]/.test(password)) {
    s++;
    check3.style.visibility = "visible";
  }else{
    check3.style.visibility = "hidden";
  }
  if (/[a-z]/.test(password)) {
    check2.style.visibility = "visible";
  }else{
    check2.style.visibility = "hidden";
  }
  if (/[0-9]/.test(password)) {
    s++;
    check5.style.visibility = "visible";
  }else{
    check5.style.visibility = "hidden";
  }
  if (/[^A-za-z0-9]/.test(password)) {
    s++;
    check4.style.visibility = "visible";
  }else{
    check4.style.visibility = "hidden";
  }
  if (password.length == 0) {
    check1.style.visibility = "hidden";
    check2.style.visibility = "hidden";
    check3.style.visibility = "hidden";
    check4.style.visibility = "hidden";
    check5.style.visibility = "hidden";
    s = 0;
  }
  return s;
}
//hide and show password feedback functionalities
password.addEventListener("focus", function () {
  document.querySelector(".password-helper .strength-meter").style.display =
    "block";
});

//manipulate password strength meter
password.addEventListener("keyup", function (e) {
  strength_meter = document.querySelector("strength-meter");

  let password = e.target.value;
  let strength = passwordStrength(password);
  let passwordStrengthSpans = document.querySelectorAll(
    ".password-helper .strength-meter span"
  );
  strength = Math.max(strength, 1);
  passwordStrengthSpans[1].style.width = strength * 20 + "%";
  if (strength < 2) {
    passwordStrengthSpans[0].innerText = "Weak";
    passwordStrengthSpans[1].style.color = "#111";
    passwordStrengthSpans[1].style.background = "#d13636";
  } else if (strength >= 2 && strength <= 4) {
    passwordStrengthSpans[0].innerText = "Medium";
    passwordStrengthSpans[1].style.color = "#111";
    passwordStrengthSpans[1].style.background = "#e6da44";
  } else {
    passwordStrengthSpans[0].innerText = "Strong";
    passwordStrengthSpans[1].style.color = "#fff";
    passwordStrengthSpans[1].style.background = "#20a820";
  }
});

//refreshing captcha
function refreshCaptcha(){
  var img = document.images['captcha_image'];
  img.src = img.src.substring(
  0,img.src.lastIndexOf("?")
  )+"?rand="+Math.random()*1000;
}
