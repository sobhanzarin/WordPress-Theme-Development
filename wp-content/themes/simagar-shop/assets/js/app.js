jQuery(document).ready(function () {
  $("#btn-auth, #close-modal").click(function (e) {
    e.preventDefault();
    $(".login-modal").toggleClass("show");
  });

  $("#phone-nav-toggle").click(function (e) {
    $("body").toggleClass("phone-nav-open");
  });
});
