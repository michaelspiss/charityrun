// get elements
sidebar = document.getElementsByClassName('sidebar')[0];
background = document.getElementsByClassName('sidebar-background')[0];
// the button to trigger the sidebar
more = document.getElementById('more');

// hide sidebar by clicking the background
background.addEventListener("click", function (e) {
    // prevent the non-js fallback (redirect to /manage/{class})
    e.preventDefault();
    sidebar.classList.add("sidebar-hide");
    background.classList.add("sidebar-hide");
    setTimeout(function () {
        background.classList.add("hidden");
    }, 300);
});

// open sidebar by clicking the "more" button
more.addEventListener("click", function (e) {
    // prevent the non-js fallback (redirect to /manage/{class}/more)
    e.preventDefault();
    background.classList.remove("hidden");
    sidebar.classList.remove("sidebar-hide");
    background.classList.remove("sidebar-hide");
});