<?php
	include('./config.php');
	
	/* establish connection */
	$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbDatabase);
	if ($mysqli->connect_errno) {
	    printf("Connect failed: %s\n", $mysqli->connect_error);
	    exit();
	}	

	$mysqli->close();
?>
<html>
<head>
	<title>News Recommender</title>
</head>

<body>

<p>
<b>Study title:</b><br />
Recommendations and the News
</p>

<p>
<b>Purpose:</b><br />
This study is designed to assess a news recommendation system. You will try out our recommendation system and then complete a questionnaire. 
</p>

<p>
<b>Participant's rights:</b><br />
Please understand your participation is voluntary and you have the right to withdraw your consent or discontinue participation at any time without penalty or loss of benefits to which you are otherwise entitled. The alternative is not to participate. You have the right to refuse to answer particular questions. Your individual privacy will be maintained in all published and written data resulting from the study.
</p>

<p>
<b>Contact information:</b><br />
Questions: If you have any questions, concerns or complaints about this research, its
procedures, risks and benefits, contact the Protocol Director Sean Westwood at seanjw@stanford.edu
</p>

<p>
<b>Independent Contact:</b><br />
If you are not satisfied with how this study is being conducted, or if you have any concerns, complaints, or general questions about the research or your rights as a participant, please contact the Stanford Institutional Review Board (IRB) to speak to someone independent of the research team at (650)-723-2480 or toll free at
1-866-680-2906. You can also write to the Stanford IRB, Stanford University, Stanford, CA 94305-5401.
</p>


	<form action="https://stanforduniversity.qualtrics.com/SE/" method="GET">
		<input type="hidden" id="guid_input" name="guid" value="" />
		<input type="hidden" id="rversion_input" name="rversion" value ="" />
		<input type="hidden" id="SID" name="SID" value="SV_cI7ZDdlocJdBj2A" />
		<input type="submit" value="I consent" />
	</form>

	<script language="javascript">
	function guidGenerator() {
	    var S4 = function() {
	       return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
	    };
	    return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
	}
	
	guid = guidGenerator();
	rversion = Math.floor(Math.random()*2) + 1;
	
	var guid_input = document.getElementById('guid_input');
	var rversion_input = document.getElementById('rversion_input');
	
	guid_input.value = guid;
	rversion_input.value = rversion;
	</script>
</body>

</html>