    <div style='position:relative; width:50%'>
<!-- #region Jssor Slider Begin -->
    <!-- Generator: Jssor Slider Maker -->
    <!-- Source: https://www.jssor.com -->
 
    <script src="/js/jssor.slider-26.1.5.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {

            var jssor_1_options = {
              $AutoPlay: 0,
              $AutoPlaySteps: 3,
              $SlideDuration: 460,
              $SlideWidth: 148,
              $SlideSpacing: 3,
              $Cols: 4,
              $Align: 0,
              $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$,
                $Steps: 3
              },
              $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$
              }
            };

            var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

            /*#region responsive code begin*/

            var MAX_WIDTH = 980;

            function ScaleSlider() {
                var containerElement = jssor_1_slider.$Elmt.parentNode;
                var containerWidth = containerElement.clientWidth;

                if (containerWidth) {

                    var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

                    jssor_1_slider.$ScaleWidth(expectedWidth);
                }
                else {
                    window.setTimeout(ScaleSlider, 30);
                }
            }

            ScaleSlider();

            $(window).bind("load", ScaleSlider);
            $(window).bind("resize", ScaleSlider);
            $(window).bind("orientationchange", ScaleSlider);
            /*#endregion responsive code end*/
        });
    </script>
    <style>
        /* jssor slider loading skin spin css */
        .jssorl-009-spin img {
            animation-name: jssorl-009-spin;
            animation-duration: 1.6s;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
        }

        @keyframes jssorl-009-spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }


        .jssorb057 .i {position:absolute;cursor:pointer;}
        .jssorb057 .i .b {fill:none;stroke:#fff;stroke-width:2000;stroke-miterlimit:10;stroke-opacity:0.4;}
        .jssorb057 .i:hover .b {stroke-opacity:.7;}
        .jssorb057 .iav .b {stroke-opacity: 1;}
        .jssorb057 .i.idn {opacity:.3;}

        .jssora073 {display:block;position:absolute;cursor:pointer;}
        .jssora073 .a {fill:#ddd;fill-opacity:.7;stroke:#000;stroke-width:160;stroke-miterlimit:10;stroke-opacity:.7;}
        .jssora073:hover {opacity:.8;}
        .jssora073.jssora073dn {opacity:.4;}
        .jssora073.jssora073ds {opacity:.3;pointer-events:none;}
        
        .stat_box {box-sizing:border-box;position:relative; background-color: #afc2d6; border-radius:10px; width:100%; height:100%; padding:15px;}
		.stat_number {box-sizing:border-box;left:0px; width:100%;font-size:40px; text-align:center; position:absolute; top:55px;}
		.stat_bottom {box-sizing:border-box;left:0px; font-size:10px; text-align:center; position:absolute; top:110px;padding: 0px 5px; width:100%}
    </style>

    <div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;width:450px;height:150px;overflow:hidden;visibility:hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
            <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="img/spin.svg" />
        </div>
        <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:980px;height:150px;overflow:hidden;">
            <div>
                <div class='stat_box'><div class='stat_top'>Nombre de membres</div><div class='stat_number'>
<?
                 echo count($this->_circle->getMembers());
?>
</div><div class='stat_bottom'>sur 18 pour l'org</div></div>
            </div>
            <div>
                 <div class='stat_box'><div class='stat_top'>Projets en cours</div><div class='stat_number'>
<?
                 echo count($this->_circle->getProjects());
?>
</div><div class='stat_bottom'>dont 6 terminés ce mois</div></div>
            </div>
            <div>
                 <div class='stat_box'><div class='stat_top'>Réunions planifiées</div><div class='stat_number'>
<?
                 echo count($this->_circle->getMeetings());
?>
</div><div class='stat_bottom'>déjà 60 réunions terminées</div></div>
            </div>            
            <div>
                 <div class='stat_box'><div class='stat_top'>Nombre de rôles</div><div class='stat_number'>
<?
                 echo count($this->_circle->getRoles());
?>
                 </div><div class='stat_bottom'>soit 3 par personne en moyenne</div></div>
            </div>
            <div>
                 <div class='stat_box'><div class='stat_top'>Durée moyenne de réunions</div><div class='stat_number'>1h25</div><div class='stat_bottom'>pour une moyenne globale de 1h25</div></div>
            </div>
           <div>
                 <div class='stat_box'><div class='stat_top'>Tensions en attente</div><div class='stat_number'>12</div><div class='stat_bottom'>20 traitée par réunion</div></div>
            </div>
           <div>
                 <div class='stat_box'><div class='stat_top'>Bugs signalés</div><div class='stat_number'>18</div><div class='stat_bottom'>dont 6 traités</div></div>
            </div> 


            <a data-u="any" href="https://www.jssor.com" style="display:none">carousel html</a>
        </div>
        <!-- Bullet Navigator -->
        <!--<div data-u="navigator" class="jssorb057" style="position:absolute;bottom:12px;right:12px;" data-autocenter="1" data-scale="0.5" data-scale-bottom="0.75">
            <div data-u="prototype" class="i" style="width:16px;height:16px;">
                <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                    <circle class="b" cx="8000" cy="8000" r="5000"></circle>
                </svg>
            </div>
        </div>-->
        <!-- Arrow Navigator -->
        <div data-u="arrowleft" class="jssora073" style="width:30px;height:30px;top:0px;left:1px;" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M4037.7,8357.3l5891.8,5891.8c100.6,100.6,219.7,150.9,357.3,150.9s256.7-50.3,357.3-150.9 l1318.1-1318.1c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3L7745.9,8000l4216.4-4216.4 c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3l-1318.1-1318.1c-100.6-100.6-219.7-150.9-357.3-150.9 s-256.7,50.3-357.3,150.9L4037.7,7642.7c-100.6,100.6-150.9,219.7-150.9,357.3C3886.8,8137.6,3937.1,8256.7,4037.7,8357.3 L4037.7,8357.3z"></path>
            </svg>
        </div>
        <div data-u="arrowright" class="jssora073" style="width:30px;height:30px;top:0px;right:1px;" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M11962.3,8357.3l-5891.8,5891.8c-100.6,100.6-219.7,150.9-357.3,150.9s-256.7-50.3-357.3-150.9 L4037.7,12931c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3L8254.1,8000L4037.7,3783.6 c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3l1318.1-1318.1c100.6-100.6,219.7-150.9,357.3-150.9 s256.7,50.3,357.3,150.9l5891.8,5891.8c100.6,100.6,150.9,219.7,150.9,357.3C12113.2,8137.6,12062.9,8256.7,11962.3,8357.3 L11962.3,8357.3z"></path>
            </svg>
        </div>
    </div>
    <!-- #endregion Jssor Slider End -->
</div>
