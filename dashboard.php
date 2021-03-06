<?php session_start(); //ouverture de la session
    //Récuperation du pseudo
    $pseudo=$_SESSION["pseudo"];
    if ($pseudo==""){
        header('Location: index.php');
    }
    //Récuperation du dossier usilisateur correspondant au pseudo
    $current_dir=$_SESSION['currentDir'];
    $oui=explode('/',$current_dir);
    $affiche ='/';
    for ($i=2;$i<count($oui)-1;$i++){
    $affiche.= $oui[$i].'/';
    }
?> 
<!DOCTYPE html>
<html>
    <head>
        <title>Homepage</title>
        <link rel="icon" type="image/png" href="/images/cloud.png" />
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="css/main.css"/>
        <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet"> <!-- permet d'utiliser des polices d'écritures différentes -->
        <link rel="stylesheet" href="css/dashboard.css"/>
    </head>

    <!-- Actions de navigation -->
    <nav>
        <div id="monicone">
            <img src=/images/cloud.png title='icone' id='icone'>
        </div>
        <div id='nav'>
            
            <!--<input type='text' placeholder='Rechercher'>-->
            <h1>Bonjour <?php echo $pseudo; ?></h1>
            <div id='icoParam'>
                <a href="parametres.php" class='param'><img src="images/parametre.png" class='ico'/></a>
                <a href="deconnexion.php"class='param'><img src="images/logout.png" class='ico'/></a>
            </div>
        </div>
    </nav>
    <body>
        <!-- barre de navigation à gauche de l'écran -->
        <div class="sidenav">
            <button id='nouveauBtn'><img src="images/plus.png" id='plus'/> Nouveau</button>
            <ul id="nouveau">
                <li><button id="dirButton">Créer un dossier</button></li>
                <li><button id="fileButton">Importer un fichier</button></li>
            </ul>
            <button onclick="alert('Bientot disponible ;)')"><img src="images/myFiles.png" id='plus'/>Mon Cloud</button>
            <button onclick="alert('Bientot disponible ;)')"><img src="images/share.png" id='plus'/>Partagé</button>
            <button onclick="alert('Bientot disponible ;)')"><img src="images/important.png" id='plus'/>Important</button>
        </div>
        <!-- Affichage  fichiers et dossiers -->
        <div id="stockage">
            
            
            <div id="dossiers">
                <h5><?php echo $affiche;?></h5> <!-- affiche le dossier courant -->
                <a href='deplacement.php?return=ok' id='returnBtn'><img src="images/retour.png" class="dirIcone"/></a> <!-- remonter au dossier parent -->
                <h2>Dossiers</h2>
                <?php
                    $dossiers=explode("$current_dir/",shell_exec("ls -d $current_dir/*/")); // Cherche tous les dossiers dans le dossier courant
                    $nb_dossiers= count( $dossiers );
                    for ($i=1;$i<($nb_dossiers);$i++){ // depart de 1 pour ne pas compter le premier vide
                ?>
                        <button class='actionDirButton' type='button'><img src="images/dossier.jpg" title='<?php echo "$dossiers[$i]"?>' alt= '<?php echo "$dossiers[$i]"?>' class="dirIcone"/>
                        <br/>
                        <?php echo "$dossiers[$i]"?></button>
                        
                    <?php }?>
                <output aria-live="polite"></output>
            </div>
            <div id="fichiers">
                <h2>Fichiers</h2>
                <?php
                    $fichiers=explode("\n",shell_exec("ls -F $current_dir| grep -v '/$'")); // cherche tous les fichiers dans le dossier courant
                    $nb_fichiers= count( $fichiers );
                    for ($i=0;$i<($nb_fichiers-1);$i++){ // -1 pour ne pas compter le dernier \n du ls
                        if (strlen($fichiers[$i])>13){
                            $affiche=substr($fichiers[$i],0,10).'...'; // si le nom du fichier est trop grand, affiche seulement les 10 premiers caractères
                        }
                        else{
                            $affiche=$fichiers[$i];
                        }
                        $image="images/";
                        $extention=explode(".",$fichiers[$i]);
                        $extention=end($extention); //affiche une icone differente en fonction de l'extention du fichier
                        if ( strcasecmp($extention,"png")==0 || strcasecmp($extention,"psd")==0 || strcasecmp($extention,"jpg")==0 || strcasecmp($extention,"jpeg")==0 || strcasecmp($extention,"gif")==0){
                            $image.="img.png";
                        }
                        elseif(strcasecmp($extention,"avi")==0 || strcasecmp($extention,"mov")==0 || strcasecmp($extention,"mkv")==0 || strcasecmp($extention,"wmv")==0){
                            $image.="video.png";
                        }
                        elseif(strcasecmp($extention,"mp3")==0 || strcasecmp($extention,"ogg")==0 || strcasecmp($extention,"flac")==0 || strcasecmp($extention,"wav")==0){
                            $image.="music.png";
                        }
                        else{
                            $image.="fichier.png";
                        }
                ?>
                        
                        <button class='actionFileButton' type='button' draggable="true"><img draggable="false" src=<?php echo $image?> title='<?php echo "$affiche"?>' alt= '<?php echo "$affiche"?>' class="fileIcone"/>
                        <br/>
                        <?php echo "$affiche"?></button>
                        
                    <?php }?>
                    
                
                <output aria-live="polite"><?php if ($_SESSION['fileUpload']){echo "Fichiers importés";
                                                $_SESSION['fileUpload']=False;}?></output>
            </div>
        </div>

        <!-- Boite de dialogue création dossier -->
        <dialog id="dirDialog">
            <h1>Attention ! Il ne doit pas y avoir d'espaces</h1>
            <form method="dialog">
                <input type='text' name='nom_fichier' id="select"/>
                <menu>
                <button value="cancel">Annuler</button>
                <button id="confirmBtn" value="default">Confirmer</button>
                </menu>
            </form>
        </dialog>

        <!-- Boite de dialogue importation fichier -->
        <dialog id="fileDialog">
            <form action="add_file.php" method="post" enctype="multipart/form-data">
                
                <input type="file" name="fileToUpload" id="fileToUpload"/>
                <input type="submit" value="Upload" name="submit"/>
            </form>
            <button id="cancelfileDialog">Cancel</button>
        </dialog>

        <!-- Boite de dialogue actions fichier -->
        <dialog id="actionFileDialog">
                <h1 id="nom_file"></h1>
                <a href="<?php echo $current_dir ?>/" Download id="fileDownloader">Télécharger</a>
                <!-- <button type="button" id="renameFile">Renommer</button>-->
                <button type="button" id="suppFile" >Supprimer</button>
                <button value="cancel" id="cancelFileButton">Annuler</button>
        </dialog>

        <!-- Boite de dialogue actions dossier -->
        <dialog id="actionDirDialog">
                <h1 id="nom_dir"></h1>
                <!-- <button type="button" id="renameDir">Renomer</button> -->
                <button type="button" id="suppDir" >Supprimer</button>
                <button value="cancel" id="cancelDirButton">Annuler</button>
        </dialog>
        
        <!-- permet d'utiliser les fonction jquery -->
        <script src="js/jquery-3.4.1.min.js"></script>

        <!-- script en rapport avec les fichiers et leur actions -->
        <script type= "text/javascript">
            var nb_files = <?php echo json_encode($fichiers); ?>;
            var actionFileDialog= document.getElementById('actionFileDialog');
            var cancelFileButton= document.getElementById('cancelFileButton');
            const dirUser = document.getElementById('fileDownloader').href;
            var suppFile = document.getElementById('suppFile');
            var fileSelected = '';
            var draggedElement;


            for (var i in nb_files){
                if (i==nb_files.length-1){
                    break
                }
                var fileIcon = document.getElementsByClassName('actionFileButton')[i];

                (function (arg1){
                    fileIcon.addEventListener('contextmenu', function onOpen(e) {
                        document.getElementById('nom_file').innerHTML=arg1
                        if (typeof actionFileDialog.showModal === "function") {
                            actionFileDialog.showModal();
                            document.getElementById('fileDownloader').href+=arg1;
                            fileSelected+=arg1;
                            
                            
                        } else {
                            window.alert("L'API dialog n'est pas prise en charge par votre navigateur");
                        }
                        e.preventDefault();
                    });
                
                    cancelFileButton.addEventListener('click', function() {
                        document.getElementById('fileDownloader').href=dirUser;
                        actionFileDialog.close('Annulé');
                        fileSelected='';   
                    });

                    suppFile.addEventListener('click',function(){
                        if (fileSelected!=''){
                        const xhttp = new XMLHttpRequest();
                        const destination='supp.php?fichier='+fileSelected;
    
                        xhttp.open("GET",destination);
                        xhttp.send();
                        fileSelected='';}
                        document.location.href="dashboard.php"; 
                    });

                    fileIcon.addEventListener('dragstart',function(e){
                        draggedElement = arg1;
                    });

                })(nb_files[i]);
            }
        </script>

        <!-- script en rapport avec les dossiers et leur actions -->
        <script type= "text/javascript">
            var nb_dossiers = <?php echo json_encode($dossiers); ?>;
            var actionDirDialog= document.getElementById('actionDirDialog');
            var cancelDirButton = document.getElementById('cancelDirButton');
            //const dirUser = document.getElementById('fileDownloader').href;
            var suppDir = document.getElementById('suppDir');
            var dirSelected = '';
            


            for (var i in nb_dossiers){
                if (i==0){
                    continue
                }
                var dirIcon = document.getElementsByClassName('actionDirButton')[i-1];

                (function (arg1){
                    dirIcon.addEventListener('contextmenu', function onOpen(e) {
                        document.getElementById('nom_dir').innerHTML=arg1
                        if (typeof actionDirDialog.showModal === "function") {
                            actionDirDialog.showModal();
                            dirSelected+=arg1;
                            
                        } else {
                            window.alert("L'API dialog n'est pas prise en charge par votre navigateur");
                        }
                        e.preventDefault();
                    });

                    cancelDirButton.addEventListener('click', function() {
                        actionDirDialog.close('Annulé'); 
                        dirSelected='';  
                    });

                    dirIcon.addEventListener('dblclick',function(){
                        
                        const xhttp = new XMLHttpRequest();
                        const destination='deplacement.php?nom_dossier='+arg1;
    
                        xhttp.open("GET",destination);
                        xhttp.send();
                        document.location.href="dashboard.php";
                       
                    });

                    suppDir.addEventListener('click',function(){
                        if (dirSelected!=''){
                        const xhttp = new XMLHttpRequest();
                        const destination='supp.php?dossier='+dirSelected;
    
                        xhttp.open("GET",destination);
                        xhttp.send();
                        dirSelected='';}
                        document.location.href="dashboard.php"; 
                    });
                    dirIcon.addEventListener('drop', function(e) {
                        e.preventDefault(); // Annule l'interdiction de drop

                        //alert('Fonction non disponible');
                        console.log(arg1);
                        console.log(draggedElement);
                        const xhttp = new XMLHttpRequest();
                        const destination='deplacement.php?dossier='+arg1+"&fichier="+draggedElement;
                        
                        xhttp.open("GET",destination);
                        xhttp.send();
                        
                        document.location.href="dashboard.php";

                    });
                    dirIcon.addEventListener('dragover', function(e) {
                        e.preventDefault(); // Annule l'interdiction de drop
                        
                    });
                })(nb_dossiers[i]);
            }
        </script>
        <script type="text/javascript" src="js/dashboard.js"></script>
    </body>
</html>