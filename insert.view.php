<fieldset>
    <legend>
        [ Neuen Datensatz einf&uuml;gen ]
    </legend>
    <form action="insert.php" method="POST">
        <table border="0" cellpadding="10">
            <tbody>
                <tr>
                    <td>
                        Systole:
                    </td>
                    <td>
                        <input type="text" name="systolic" value="" size="25" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Diastole:
                    </td>
                    <td>
                        <input type="text" name="diastolic" value="" size="25" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="Eintragen" name="submit" />
                    </td>
                    <td>
                        <input type="reset" value="Zur&uuml;cksetzen" name="reset" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</fieldset>