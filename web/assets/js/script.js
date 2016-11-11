function EmotionsDisplay()
{
    this.gallery = $('#gallery');
    this.emotionsContainer = $('#photo-emotions');
}

EmotionsDisplay.prototype.init = function () {

    var self = this;

    this.gallery
        .on('init beforeChange', function(event, slick, currentSlide, nextSlide){

            var slideIndex = typeof nextSlide == 'undefined' ? 0 : nextSlide;
            var slideImg = $(this).find('img').eq(slideIndex + 1);

            //console.log(slideIndex, slideImg[0]);

            self.emotionsContainer.html(self.renderEmotions(slideImg));
        })
        .slick({
            adaptiveHeight: true,
            arrows: true
        });
};

EmotionsDisplay.prototype.renderEmotions = function (image) {

    var emotions = image.data('imagesEmotions');

    var content = $('<div id="emotions-content">');

    if (!emotions.length) {
        content.html('<div>No faces detected in this photo!</div>');
    } else {
        content.html('<div class="count-faces">' + emotions.length + ' face(s) detected in this photo:</div>');
        for (var i=0; i< emotions.length; i++) {

            var face = emotions[i];
            var facePosition = face.faceRectangle;
            var scores = face.scores;

            var maxEmotion = null;
            for (var emotionLabel in scores) {
                if (!maxEmotion || scores[emotionLabel] > scores[maxEmotion]) {
                    maxEmotion = emotionLabel;
                }
            }

            var renderedFaceEmotions = $('<div class="emotion">').html(maxEmotion + ', precision ' + scores[maxEmotion]);

            content.append(renderedFaceEmotions);
        }
    }

    return content;
};

$(document).ready(function () {

    var emotionsDisplay = new EmotionsDisplay();
    emotionsDisplay.init();

});
