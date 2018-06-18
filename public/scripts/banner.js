var next_image = false;
function displayBanner(data) {
    var img = $("#banner_image");
    var front = img.find("div.front_image");
    var back = img.find("div.back_image");
    var description = $("#banner_description");

    description.find("a.project_title").attr("href", "project.php?id=" + data["id"]);
    if (!next_image) {
        front.css("background-image", "url(" + data["banner_path"] + ")");
        next_image = "back";
        description.find("a.project_title").html(data["title"]);
        description.find("div.project_description").html(data["description"]);
        description.find("div.project_downloads").html(data["downloads"] + " downloads");
    } else {
        if (next_image === "front") {
            front.css("background-image", "url(" + data["banner_path"] + ")");
            front.slideDown(function () {
                back.slideUp();
            });
            next_image = "back";
        } else if (next_image === "back") {
            back.css("background-image", "url(" + data["banner_path"] + ")");
            back.slideDown(function () {
                front.slideUp();
            });
            next_image = "front";
        }
        description.fadeOut(function() {
            description.find("a.project_title").html(data["title"]);
            description.find("div.project_description").html(data["description"]);
            description.find("div.project_downloads").html(data["downloads"] + " downloads");
            description.fadeIn();
        });
    }
}

function BannerTimer(maxIndex, timeOut) {
    this.existingBanners = [];
    this.requestNum = 0;
    this.timeOut = timeOut;
    this.maxIndex = maxIndex;

    this.start = function() {
        if (this.existingBanners[this.requestNum] !== undefined) {
            this.loadData(this.existingBanners[this.requestNum]);
            return;
        }
        if (this.requestNum >= this.maxIndex)
            this.requestNum = 0;
        var self = this;
        $.ajax({
            type: "POST",
            url: "scripts/ajax/get_banner_data.php",
            data: {
                requestBannerData: this.requestNum
            },
            success: function(data) {
                if (data && data.success) {
                    self.loadData(data);
                }
            },
            error: function(xmlHttp) {
                console.log("error:");
                console.log(xmlHttp.responseText);
                //$("<div id='debugger'></div>").html(xmlHttp.responseText).appendTo("body");
            }
        });
    };

    this.loadData = function(data) {
       // console.log(data); // debugging only!!
        if (this.existingBanners[this.requestNum] === undefined) {
            this.existingBanners[this.requestNum] = data;
        }
        this.requestNum = data["nextNum"];

        var self = this;
        var timeOut = this.timeOut;
        if (next_image === false)
            timeOut *= 0.6; // speed up first banner (due to no data transfer)
        displayBanner(data);

        window.setTimeout(function() {
            self.start();
        }, timeOut);

    };
}

$(document).ready(function() {
    var bt = new BannerTimer(5, 6000);
    bt.start();
});
