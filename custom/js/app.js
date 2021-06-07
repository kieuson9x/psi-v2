$(function () {
  $('[data-toggle="offcanvas"]').on("click", function () {
    $(".offcanvas-collapse").toggleClass("open");
    $(".offcanvas-collapse .nav-item.navigation-item").toggleClass("hidden");
  });
});
