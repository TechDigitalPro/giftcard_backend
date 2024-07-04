<div class="visibility-hidden">
    <div class="background-audio-box">
        <?php
        $music_url_list = array(
            $base_url."assets/user/audio/Sia - Cheap Thrills Ft. Sean Paul [Lyrics].mp3",
            $base_url."assets/user/audio/David Guetta - Titanium (Lyrics) ft. Sia.mp3",
            $base_url."assets/user/audio/Imagine Dragons - Not Today from ME BEFORE YOU.mp3",
            $base_url."assets/user/audio/Feels Like Home.mp3"
        )
        ?>
        <audio id="background-audio" <!--autoplay-->>
        <source src="<?php echo $music_url_list[0]; ?>?v=<?php echo TIMESTAMP; ?>" type="audio/mpeg">
        </audio>
    </div>
    <script>
        var music_url_list = <?php echo json_encode($music_url_list); ?>;
        console.log(music_url_list);
        var bg_audio = document.getElementById("background-audio");
        bg_audio.muted = false;
        function a_next(n){
            bg_audio.setAttribute('src', music_url_list[n]);
            bg_audio.play();
        }
        var _next = 0;
        var len = music_url_list.length;

        bg_audio.addEventListener("ended", function(){
            _next += 1;
            a_next(_next);
            console.log(len, _next);
            if((len-1)==_next) {
                _next=-1;
            }
        });

        var f_show_dialog = true;

        $(document).ready(function(){
            var enable_audio = $.cookie('enable_audio');
            console.log("enable_audio", enable_audio);
            if(enable_audio == '0') {
                $(".audio-play-btn").attr("class", "audio-play-btn volume-off");
                $(".audio-play-btn").find("i").attr("class", "fa fa-volume-off");
            }else{
                $(".audio-play-btn").attr("class", "audio-play-btn volume-up");
                $(".audio-play-btn").find("i").attr("class", "fa fa-volume-up");
                //$("#background-audio").get(0).play();
                try{
                    bg_audio.play();
                    f_show_dialog = false;
                }catch(e){
                    console.log('catch', e);
                }finally {
                    setTimeout(function(){
                        if(bg_audio.paused){
                            //window.location.reload(true);
                            show_confirmFullDlg("Kerart.cn " + lang("wants to play music"), function(){
                                bg_audio.play();
                            }, function(){
                                $(".audio-play-btn").attr("class", "audio-play-btn volume-off");
                                $(".audio-play-btn").find("i").attr("class", "fa fa-volume-off");
                                $.cookie('enable_audio', '0', { expires: 365, path: '/' });
                                bg_audio.pause();
                            });
                        }
                    }, 50);
                    console.log('finally', f_show_dialog);
                }
            }

            $("body").on("click", ".audio-play-btn", function(){
                var ths = $(this);
                var audio_enabled = $.cookie('enable_audio');
                if(audio_enabled == '0') {
                    $(".audio-play-btn").attr("class", "audio-play-btn volume-up");
                    ths.find("i").attr("class", "fa fa-volume-up");
                    $.cookie('enable_audio', '1', { expires: 365, path: '/' });
                    bg_audio.play();
                } else {
                    $(".audio-play-btn").attr("class", "audio-play-btn volume-off");
                    ths.find("i").attr("class", "fa fa-volume-off");
                    $.cookie('enable_audio', '0', { expires: 365, path: '/' });
                    bg_audio.pause();
                }
            });
        });
    </script>
</div>