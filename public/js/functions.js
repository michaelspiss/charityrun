function updateStats() {
    var associations = {
        "km_run": km_run,
        "km_left_to_go": Math.max((total_km - km_run), 0),
        "total_rounds": total_rounds,
        "total_donations": total_donations
    };
    for(var id in associations) {
        if(associations.hasOwnProperty(id) && document.getElementById(id) !== null) {
            document.getElementById(id).innerText = associations[id];
        }
    }
    draw_progress_lines();
}

function getAsync(url, callback)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200)
            callback(xmlHttp.response);
    };
    xmlHttp.open("GET", url, true); // true for asynchronous
    xmlHttp.responseType = "json";
    xmlHttp.send(null);
}

function doConfirm(){
    return confirm('Are you sure?');
}