<?php
require_once __DIR__ . '/db.php';
$connect = dbConnect();
$query ="SELECT `naam`,`ELO` FROM totaal2raw";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html>
     <head>
          <title>Gronings klaverjasblok - statistieken</title>
          <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
          <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
          <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
          <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" />
          <style>
          .table-nonfluid {
                               width: auto !important;
                            }
          .text-large {
                          font-size: 150%;
                        }

          </style>


     </head>
     <body>
       <div class="container-fluid">
       <div class="row">
         <div class="col-xs-12" role="main">
       <p>
         <ul class="nav nav-pills nav-justified">
          <li class="nav-item">
            <a class="nav-link active" href="index.php">Start</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="scoreboard.php">Scoreboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="statsall.php">Alle stats</a>
          </li>
       </p>

              <blockquote>
                <p>“In God we Trust, all others bring data"</p>
                <footer>William Edwards Deming</footer>
                 </blockquote>



                    <table id="statistieken" class="table table-nonfluid">
                         <thead>
                              <tr>
                                   <td>Nr</td>
                                   <td>Naam</td>
                                   <td>ELO<br>score</td>
                              </tr>
                         </thead>
                         <?php
                         $i = 1;
                         $leeg = "";
                         $text = "De scores worden berekend, kom zo terug.";

                         while($row = mysqli_fetch_array($result))
                         {
                           if($row == "") {
                             echo '
                             <tr>
                                 <td>'.$i.'</td>
                                 <td>'.$text.'</td>
                                  <td>'.$leeg.'</td>
                             </tr>
                             ';
                             $i = $i + 1;


                           }


                           if($row["ELO"] !=  "") {
                              echo '
                              <tr>
                                  <td>'.$i.'</td>
                                   <td><a href="charts.php?speler='.$row["naam"].'">'.$row["naam"].'</a>'.(($i==1)?' <font color="#D4AF37"><i class="fa fa-trophy"></i></font>':'').(($i==2)?' <font color="#C0C0C0"><i class="fa fa-medal"></i></font>':'').(($i==3)?' <font color="#cd7f32"><i class="fa fa-medal"></i></font>':'').'</td>
                                   <td>'.$row["ELO"].'</td>
                              </tr>
                              ';
                              $i = $i + 1;
                          }

                         }
                         ?>
                    </table>

               <p class="footer text-center text-muted">
                © 2018-2020 <a href="mailto:breinink@gmail.com">brein inc.</a> &nbsp;

              </p>
               </div>
               </div>
               </div>


     </body>
</html>
<script>

$(document).ready(function(){
     $('#statistieken').DataTable({
        "searching": false,
        "paging": true,
        "info": false,
        "lengthChange":false
    });
});
</script>
