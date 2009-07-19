              function emailAweber(aweber) {
                var email = $("input#email").val();
                var datastring = "email="+email+"&toaddy=" + aweber;
                $.ajax({
                    type: "POST",
                    url: "/wp-content/plugins/feedweber/emailsignup.php",
                    data: datastring,
                    success: function(msg) {
                    $('#resultarea').html(msg);
                    }
                  });
                  return false;
              }