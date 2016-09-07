

<html>
<body>

<form action="test.php" method= "post">
<table width = "500" border ="5" align = "center">
<tr> 
<td> <h1> Demographics </h1>
</tr>
<tr> <td> Name : </td> 
      <td><input type="text" name="name"> </td> 
	  </tr>

<tr> <td> Age : </td> 
      <td><input type="text" name="age"> </td> 
	  </tr>
<tr> <td> Income : </td> 
      <td><input type="text" name="income"> </td> 
	  </tr>

<tr> 
      <td> <input type="submit" name="submit"  value="submit now"></td> 
	  </tr>
	  </table>
	  </form>
	  </body>
</html>
	  
	  <?php
	  if(isset($_POST['submit'])){
	
	echo $name=$_POST['name'];
	echo $age=$_POST['age'];
	echo $income=$_POST['income'];
	
	
	}
?>
	 
	  









