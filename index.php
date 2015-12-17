<html>
    <head>
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/style.css" rel="stylesheet" type="text/css"/>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="js/chart.js/chart.core.js" type="text/javascript"></script>
        <script src="js/chart.js/chart.line.js" type="text/javascript"></script>
        <script src="js/chart.js/chart.bar.js" type="text/javascript"></script>
    </head>
    <body>
        <div>
            <ul class="canvas-list">
                <li>
                    <h1>Langzeitwentwicklung</h1>
                    <canvas id="historychart" width="620" height="350"></canvas>
                </li>
                <li>
                    <h2>Kurzzeitentwicklung</h2>
                    <canvas id="latestchart" width="620" height="350"></canvas>
                </li>
                <li>
                    <h2>Median pro Quartil</h2>
                    <canvas id="barchart" width="620" height="350"></canvas>
                </li> 
                <li>
                    <h2>Durchschnittswerte pro Quartil</h2>
                    <canvas id="trendchart" width="620" height="350"></canvas>
                </li>

            </ul>
        </div>
        <br />
        <br />
        <a href="#" id="createButton" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>F&uuml;tter mich...</a>

        <div id="createDialog" title="Werte eintragen" style="display:none;">
            <fieldset>
                <legend>
                    [ Neuen Datensatz einf&uuml;gen ]
                </legend>
                <span style="color:crimson;" id="output"></span>
                <form id="createForm">
                    <table border="0" cellpadding="10">
                        <tbody>
                            <tr>
                                <td>
                                    Systole:
                                </td>
                                <td>
                                    <input type="number" name="systolic" value="120" size="25" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Diastole:
                                </td>
                                <td>
                                    <input type="number" name="diastolic" value="80" size="25" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Puls:
                                </td>
                                <td>
                                    <input type="number" name="pulse" value="60" size="25" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Datum:
                                </td>
                                <td>
                                    <input type="date" id="date" name="date" size="25" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="submit" name="submit" value="Eintragen" />
                                </td>
                                <td>
                                    <input type="reset" value="Zur&uuml;cksetzen" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </fieldset>
        </div>
    </body>

    <script type="text/javascript">
        $(function () {
            //initial calls
            $('#createDialog').dialog({
                width: 600,
                autoOpen: false,
                hide: {effect: 'scale', duration: 800},
                show: {effect: 'scale', duration: 800},
                modal: true
            });


            //events
            $('#createButton').click(function () {
                $('#createDialog').dialog('open');
            });

            $('#createForm').submit(function () {
                $.post('ajaxfacade.php',
                        {
                            data: $(this).serialize()
                        }, function (data) {
                    //re-render charts

                });
                return false;
            });


            //config
            var lineChartOptions = {datasetFill: false};
            var colorsSystole = new Array(37, 128, 57);
            var colorsDiastole = new Array(49, 149, 184);
            var colorsPulse = new Array(207, 55, 33);
            var alternateColor = "#fff";

            var lineDataSets = {
                labels: [],
                datasets: [
                    {
                        fillColor: getRGBAString(colorsSystole, 0.2),
                        strokeColor: getRGBAString(colorsSystole, 1),
                        pointColor: getRGBAString(colorsSystole, 1),
                        pointStrokeColor: alternateColor,
                        pointHighlightFill: alternateColor,
                        pointHighlightStroke: getRGBAString(colorsSystole, 1)
                    },
                    {
                        fillColor: getRGBAString(colorsDiastole, 0.2),
                        strokeColor: getRGBAString(colorsDiastole, 1),
                        pointColor: getRGBAString(colorsDiastole, 1),
                        pointStrokeColor: alternateColor,
                        pointHighlightFill: alternateColor,
                        pointHighlightStroke: getRGBAString(colorsDiastole, 1)
                    },
                    {
                        fillColor: getRGBAString(colorsPulse, 0.2),
                        strokeColor: getRGBAString(colorsPulse, 1),
                        pointColor: getRGBAString(colorsPulse, 1),
                        pointStrokeColor: alternateColor,
                        pointHighlightFill: alternateColor,
                        pointHighlightStroke: getRGBAString(colorsPulse, 1)
                    }
                ]
            };

            var barDataSets = {
                labels: [],
                datasets: [
                    {
                        fillColor: getRGBAString(colorsSystole, 0.5),
                        strokeColor: getRGBAString(colorsSystole, 0.8),
                        highlightFill: getRGBAString(colorsSystole, 0.75),
                        highlightStroke: getRGBAString(colorsSystole, 1),
                    }, {
                        fillColor: getRGBAString(colorsDiastole, 0.5),
                        strokeColor: getRGBAString(colorsDiastole, 0.8),
                        highlightFill: getRGBAString(colorsDiastole, 0.75),
                        highlightStroke: getRGBAString(colorsDiastole, 1),
                    }, {
                        fillColor: getRGBAString(colorsPulse, 0.5),
                        strokeColor: getRGBAString(colorsPulse, 0.8),
                        highlightFill: getRGBAString(colorsPulse, 0.75),
                        highlightStroke: getRGBAString(colorsPulse, 1),
                    }
                ]
            };

            //helper methods
            function median(values) {

                values.sort(function (a, b) {
                    return parseInt(a) - parseInt(b);
                });

                var half = Math.floor(values.length / 2);

                if (values.length % 2)
                    return values[half];
                else
                    return Math.round((parseInt(values[half - 1]) + parseInt(values[half])) / 2);
            }

            function getRGBAString(values, opacity) {
                if (typeof values !== "array" || values.length !== 3) {
//                    throw "Invalid parameter count";
                }
                return "rgba(" + values[0] + "," + values[1] + "," + values[2] + "," + opacity + ")";
            }

            function reset() {
                sumSystoles = 0;
                sumDiastoles = 0;
                sumPulses = 0;
                tmpSystole = new Array();
                tmpDiastole = new Array();
                tmpPulse = new Array();
            }



            //logic
            var latestCtx = document.getElementById("latestchart").getContext("2d");
            var historyCtx = document.getElementById("historychart").getContext("2d");
            var barCtx = document.getElementById("barchart").getContext("2d");
            var trendCtx = document.getElementById("trendchart").getContext("2d");

            var historyChart = new Chart(historyCtx).Line($.extend(true, {}, lineDataSets), lineChartOptions);
            var latestChart = new Chart(latestCtx).Line($.extend(true, {}, lineDataSets), lineChartOptions);
            var medianChart = new Chart(barCtx).Bar(barDataSets);
            var trendChart = new Chart(trendCtx).Line($.extend(true, {}, lineDataSets), lineChartOptions);


            var allData =<?php include_once 'ajaxfacade.php'; ?>;
            var quartilCount = 1;


            //average
            var sumSystoles = 0;
            var sumDiastoles = 0;
            var sumPulses = 0;

            //median             
            var tmpSystole = new Array();
            var tmpDiastole = new Array();
            var tmpPulse = new Array();

            for (var i = 0, max = allData.length; i < max; i++) {
                //all data
                historyChart.addData(new Array(allData[i].systole, allData[i].diastole, allData[i].pulse), "");

                //latest data
                if (allData.length > 20 && i >= (allData.length - 20)) {
                    latestChart.addData(new Array(allData[i].systole, allData[i].diastole, allData[i].pulse), allData[i].timestamp);
                }

                //median and average chart
                count = i + 1;
                if (count % Math.round(allData.length / 4) === 0) {
                    avgSys = Math.round(sumSystoles / (allData.length / 4));
                    avgDia = Math.round(sumDiastoles / (allData.length / 4));
                    avgPul = Math.round(sumPulses / (allData.length / 4));

                    trendChart.addData(new Array(avgSys, avgDia, avgPul), "Q" + quartilCount);

                    //median
                    medianChart.addData(new Array(median(tmpSystole), median(tmpDiastole), median(tmpPulse)), "Q" + quartilCount);
                    quartilCount++;

                    reset();

                } else {
                    sumSystoles += parseInt(allData[i].systole);
                    sumDiastoles += parseInt(allData[i].diastole);
                    sumPulses += parseInt(allData[i].pulse);

                    //median
                    tmpSystole.push(parseInt(allData[i].systole));
                    tmpDiastole.push(parseInt(allData[i].diastole));
                    tmpPulse.push(parseInt(allData[i].pulse));
                }
            }
        });
    </script>
</html>
