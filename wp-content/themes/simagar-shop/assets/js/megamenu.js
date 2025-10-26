jQuery(function ($) {
  var frame;

  $(".select-menu-icon").on("click", function (e) {
    e.preventDefault();
    var button = $(this);
    if (frame) frame.close();

    frame = wp.media({
      title: "انتخاب آیکن منو",
      button: { text: "انتخاب" },
      multiple: false,
    });

    frame.on("select", function () {
      var attachment = frame.state().get("selection").first().toJSON();
      button.siblings(".menu-item-icon-id").val(attachment.id);
      button
        .siblings(".menu-item-icon-preview")
        .attr("src", attachment.url)
        .show();
      button.siblings(".remove-menu-icon").show();
    });

    frame.open();
  });

  $(".remove-menu-icon").on("click", function (e) {
    e.preventDefault();
    var button = $(this);
    button.siblings(".menu-item-icon-id").val("");
    button.siblings(".menu-item-icon-preview").hide();
    button.hide();
  });
});
