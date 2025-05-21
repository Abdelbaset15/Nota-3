

document.addEventListener("DOMContentLoaded", function() {
  var showVisaBtn = document.getElementById("showVisa");
  var showCash = document.getElementById("showCash");
  var visaDiv = document.getElementById("visaDiv");
  var visaCash = document.getElementById("visaCash");
  var backToVisaCashBtn = document.getElementById("backToVisaCach");
  var goToCheckoutEnd = document.getElementById("goToCheckoutEnd");
  var checkoutEndParent = document.getElementById("checkoutEndParent");
  var backToVisaOrCach = document.getElementById("backToVisaOrCach");
  var finish = document.getElementById("finish");
  var thankYou = document.getElementById("thankYou");

  

  showVisaBtn.addEventListener("click", function() {
      visaDiv.style.display = "flex";
      visaCash.style.display = "none";
      thankYou.style.display = "none";
  });

  backToVisaCashBtn.addEventListener("click", function() {
      visaDiv.style.display = "none";
      visaCash.style.display = "flex";
      thankYou.style.display = "none";
  });
  
  backToVisaOrCach.addEventListener("click", function() {
    visaDiv.style.display = "none";
    visaCash.style.display = "flex";
    checkoutEndParent.style.display = "none";
    thankYou.style.display = "none";
  });

  goToCheckoutEnd.addEventListener("click", function() {
      visaDiv.style.display = "none";
      visaCash.style.display = "none";
      checkoutEndParent.style.display = "flex";
      thankYou.style.display = "none";
  });

  finish.addEventListener("click", function() {
      visaDiv.style.display = "none";
      visaCash.style.display = "none";
      checkoutEndParent.style.display = "none";
      thankYou.style.display = "flex";
  });

  showCash.addEventListener("click", function() {
      visaDiv.style.display = "none";
      visaCash.style.display = "none";
      checkoutEndParent.style.display = "flex";
      thankYou.style.display = "none";
  });
});
