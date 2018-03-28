var map = AmCharts.makeChart( "map", {
    "type": "map",
    "theme": "light",
    "projection": "equirectangular",
    "mouseEnabled": false,
    "dragMap": false,
    "zoomOnDoubleClick": false,

    "dataLoader": {
        "url": "map/json"
    },
    "zoomControl": {
        "zoomControlEnabled": false,
        "homeButtonEnabled": false
    },
    "linesSettings": {
        "arc": -0.7, // this makes lines curved. Use value from -1 to 1
        "color": "#000000",
        "alpha": 0.4,
        "thickness": 2,
        "bringForwardOnHover": false
    }
});

map.ready = false;
map.addListener("init", function () {
    map.ready = true;
    draw_progress_lines();
});

map.addListener("resized", function () {
    draw_progress_lines();
});

function draw_progress_lines () {
    if(!map.ready) {
        return;
    }
    var i = 1;
    while(map.getObjectById("progress_"+i) !== undefined) {
        var line = map.getObjectById("progress_"+i).lineSvg.node;
        var length = line.getTotalLength();
        var percent_run = Math.min(100, km_run / map.getObjectById("progress_"+i).customData.distance * 100);
        var one_percent = length / 100;
        line.setAttribute("stroke-dasharray", length);
        line.setAttribute("stroke-dashoffset", -length + (one_percent * percent_run));
        i++;
    }
}

// map.getObjectById("one").lineSvg.node.attr("d")