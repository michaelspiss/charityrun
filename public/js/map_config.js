var map = AmCharts.makeChart( "map", {
    "type": "map",
    "theme": "light",
    "projection": "equirectangular",
    "mouseEnabled": false,
    "dragMap": false,
    "zoomOnDoubleClick": false,

    "dataProvider": {
        "map": "worldLow",
        "lines": []
    },
    "zoomControl": {
        "zoomControlEnabled": false,
        "homeButtonEnabled": false
    }
});