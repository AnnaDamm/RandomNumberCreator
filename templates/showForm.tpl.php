<!DOCTYPE html>
<html lang="en">
    <head>
        <link type="text/css" href="css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
        <link rel="stylesheet" media="screen" type="text/css" href="css/showForm.css"/>
        <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
        <script type="text/javascript" src="js/showForm.js"></script>

    </head>
    <body>
        <script type="text/javascript">
            var refreshPage = <?php echo json_encode($refreshPage); ?>
        </script>
        <header>
            <form id="dice" action="?action=roll" method="post">
                <fieldset id="diceroll">
                    <legend>Rolling!</legend>
                    <label for="dice">Dice: </label>
                    <input type="text" name="dice" id="dice" 
                           title="Use syntax like: 4d20+5"/>
                    <input type="submit" value="Roll"/>
                </fieldset>
                <fieldset id="standardrolls">
                    <legend>Standard Rolls</legend>
                    <input type="submit" class="standardroll" value="1d6"/>
                    <input type="submit" class="standardroll" value="1d20"/>
                    <input type="submit" class="standardroll" value="3d20"/>
                </fieldset>
                <fieldset id="publish">
                    <legend>Publish</legend>
                    <input type="button" name="getLink" id="getLink" value="Get Link!"/>

                </fieldset>
                <fieldset id="clear">
                    <legend>History</legend>
                    <input type="button" name="clear" id="clear" value="Clear!"/>

                </fieldset>

            </form>
            <div id="link">
                <input type="text" name="link" value="<?php echo $linkToPage; ?>"/>
                <button name="closeLink" id="closeLink">X</button>
            </div>
        </header>
        <div id="main">
            <table id="rolls">
                <caption>Last Rolls</caption>
                <colgroup>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <thead>
                    <tr>
                        <th class="dice">Dice</th>
                        <th class="result">Result</th>
                        <th class="time">Time</th>
                    </tr>
                </thead>
                <tbody id="rollresults">
                    <?php if ($returnArray['success'] === false) : ?>
                        <tr class="error">
                            <td colspan="3"><?php echo $returnArray["error"]; ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($sessionResults)) : ?>
                        <?php foreach ($sessionResults as $returnArray) : ?>
                            <?php if ($returnArray['success'] === true) : ?>
                                <tr>
                                    <td class="dice"><?php echo $returnArray["dice"]; ?></td>
                                    <td class="result"><?php echo $returnArray["result"]; ?></td>
                                    <td class="time"><?php echo date("H:i:s",
                            $returnArray["time"]); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
<?php endif; ?>
                </tbody>
            </table>
        </div>
        <footer>
            RPG Dice 1.2 &copy; 2011 Valinchen
        </footer>
    </body>
</html>
