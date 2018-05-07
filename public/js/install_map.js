var map = AmCharts.makeChart( "map", {
    "type": "map",
    "theme": "light",
    "panEventsEnabled": true,
    "projection": "equirectangular",
    "zoomOnDoubleClick": false,
    "dataProvider": {
        "map": "worldLow",
        "getAreasFromMap": true
    },
    "areasSettings": {
        "autoZoom": false,
        "color": "#CDCDCD",
        "colorSolid": "#5EB7DE",
        "selectedColor": "#5EB7DE",
        "outlineColor": "#666666",
        "rollOverColor": "#88CAE7",
        "rollOverOutlineColor": "#FFFFFF",
        "selectable": true
    },
    "linesSettings": {
        "arc": -0.7, // this makes lines curved. Use value from -1 to 1
        "color": "#000000",
        "alpha": 0.4,
        "thickness": 2,
        "bringForwardOnHover": false,
        "arrowAlpha": 1,
        "arrowSize": 4,
        "arrow": "middle"
    },
    "listeners": [ {
        "event": "clickMapObject",
        "method": function( event ) {
            var info = event.chart.getDevInfo();
            if(emptyString(document.getElementById('latitude_from').value)) {
                document.getElementById('latitude_from').value = info.latitude;
                document.getElementById('longitude_from').value = info.longitude;
            } else {
                document.getElementById('latitude_to').value = info.latitude;
                document.getElementById('longitude_to').value = info.longitude;
            }

        }
    } ]
} );