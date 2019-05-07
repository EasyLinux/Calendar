$("#Logout").click(function() {
    console.log("Logout");
    $.post("ajax/menu.php", "Action=Logout", function(data, status) {
        console.log("Data: " + data + "\nStatus: " + status);
        if (data == "OUT") {
            location.reload();
        }
    });
});