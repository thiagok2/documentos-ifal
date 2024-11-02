$(document).ready(function() {
    console.log("1");
    $(".rb-rating").rating({
        showCaption: false,
        displayOnly: false,
        showCaptionAsTitle: false,
        starCaptions: {
            0: "status:baixo",
            1: "status:razoável",
            2: "status:bom",
            3: "status:excelente"
        },
        starCaptions: function(val) {
            return "Score: " + val;
        }
    });
});
