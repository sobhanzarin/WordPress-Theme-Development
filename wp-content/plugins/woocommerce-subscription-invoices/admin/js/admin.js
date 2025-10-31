jQuery(document).ready(function ($) {
  // Initialize Select2
  $(".wsi-select2").select2();

  // Load user subscriptions when user changes
  $("#user_id").on("change", function () {
    var userId = $(this).val();

    if (userId) {
      $.ajax({
        url: wsi_ajax.ajax_url,
        type: "POST",
        data: {
          action: "wsi_get_user_subscriptions",
          user_id: userId,
          nonce: wsi_ajax.nonce,
        },
        success: function (response) {
          if (response.success) {
            var $subscriptionSelect = $("#subscription_id");
            $subscriptionSelect.empty();

            $.each(response.data, function (index, option) {
              $subscriptionSelect.append(
                $("<option>", {
                  value: option.id,
                  text: option.text,
                })
              );
            });
          }
        },
      });
    }
  });

  // Auto-fill amount when subscription is selected
  $("#subscription_id").on("change", function () {
    var subscriptionId = $(this).val();

    if (subscriptionId) {
      $.ajax({
        url: wsi_ajax.ajax_url,
        type: "POST",
        data: {
          action: "wsi_get_subscription_amount",
          subscription_id: subscriptionId,
          nonce: wsi_ajax.nonce,
        },
        success: function (response) {
          if (response.success) {
            $("#amount").val(response.data.amount);
          }
        },
      });
    }
  });

  // Set default due date to 30 days from now
  var today = new Date();
  var dueDate = new Date();
  dueDate.setDate(today.getDate() + 30);

  var dueDateString = dueDate.toISOString().split("T")[0];
  $("#due_date").val(dueDateString);
});
