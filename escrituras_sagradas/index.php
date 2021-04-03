<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../style.css" type="text/css" rel="stylesheet" />
    <link href="src/css/all.min.css" type="text/css" rel="stylesheet" />
    <script src="src/js/all.min.js" type="text/javascript"></script>
    <title>Escrituras Sagradas YHWH</title>
</head>
<body>

    <div id="center">
        <form method="get">
            <prev></prev>
            <select name="book" class="form-control">
            </select>
            <next></next>
            <button class="btn btn-default" type="submit" id="enviar">abrir</button>
        </form>

        <div id="content">
            <div id="info">
                </div>

            <div id="text">
            </div>

            <br>
            <a href="#top" class="btn btn-default">TOPO <i class="fas fa-angle-double-up"></i></a>
            <br><br>
            <a href="https://andressa-aplicativos.herokuapp.com/apps/escrituras_sagradas.apk">obter aplicativo de celular</a>
        </div> 

        

    </div>
    
</body>
</html>

<style>
    body{font-size:62.5%;}

    h3 {
        margin-bottom: -3px;
        padding: 0;
    }

    #center{
        font-family:'Kiwi Maru',sans-serif;
        margin-top:90px;
        display:flex;
        text-align:justify;
        align-items:center;
        flex-direction:column;
    }

    #center #content{
        margin-top:5px;
        margin-bottom:40px;
        word-spacing: 0.1em;
        letter-spacing: 1px;
        line-height: 1.8em;
        font-size:1.1rem;
        width: 80%;
        padding: 19px;
    }

    #content #info h1{
        text-transform:capitalize!important;
    }

    #enviar{display:none!important;}

    sub, sub a{font-weight:bold; font-size:0.7rem; color:#aaa;}

    select{cursor:pointer;}

    @media screen and (max-width:800px){

        #center #content{
            width:94%;
            font-size:1.0rem;
        }

       form{
        display:flex;
        flex-direction:column;
        margin:0 auto;
        align-items:center;
       }

        
      .form-control, select{
        width:270px;
      }

      form .btn-default{
        margin-top:5px;
        padding: 3px;
        font-size: 15px;
      }
    }
</style>


<script>

    var text = document.querySelector('div#text');
    var info = document.querySelector('div#info');
    let form = document.querySelector('form');
    let submitBtn = form.querySelector('button[type=submit]');
    let select = form.querySelector('select[name=book]');
    let selectChapt = form.querySelector('select[name=chapter]')

    let options = select.options;


    let prev = document.querySelector('prev');
    let next = document.querySelector('next');

    let allBtns = document.querySelectorAll('button')

    allBtns.forEach(function(btn){
        btn.onclick = () =>{
            if(btn.dataset.href){
                window.location.href = btn.dataset.href
            }
        }
    })

    let params = decodeURI(window.location.search.substr(1));  

    // if (params == "") { 
    //     if('params' in localStorage){
    //         window.location.href = window.location.href+localStorage.getItem("params")
    //     }else{
    //         window.location.href = window.location.href+"?book=gênesis&chapter=1"
    //     }
    // }

    //  // salva ultimo texto lido
    // localStorage.setItem("params", `?${params}`);

    localStorage.setItem("params", "?book=gênesis&chapter=1");

    let bookRegex = /(?<=book\=)(?:\d%2B[a-zA-ZÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑáàâãéèêíïóôõöúçñ]*|[a-zA-ZÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑáàâãéèêíïóôõöúçñ]*)/gm
    let chapterRegex = /(?<=chapter\=)[0-9]*/gm

    let selectedBook = params.match(bookRegex)[0].replaceAll('%2B','+');
    let selectedChapter = params.match(chapterRegex)[0].replaceAll('%2B','+');


    function getBibleBooks(){

    
    let request = new Request('src/books.json', {
        method: 'GET',
    });

    fetch(request).then(function(response) { return response.text() }).then(function(response){
        response = JSON.parse(response)
        let option = document.createElement('option');

        
        if(response['msg']){
            select.appendChild(option);
            option.value = "Erro";
            option.innerText = "erro";
            text.innerHTML = "Ocorreu um erro, tente novamente mais tarde";
        }else{

            // tudo certo


       
            let bookTitle = selectedBook.replace('+', ' ')

            info.innerHTML = `<h1>${bookTitle} ${selectedChapter}</h1>`

            response.forEach(function(item){ 
         
                
                let option = document.createElement('option'); 
                item['name'] = item['name'].trim();
                option.value = item['name'].toLowerCase().replaceAll('º','+').replaceAll('ª','+').replaceAll('+ ','+').replaceAll('lamentações de jeremias', 'lamentações');
                option.innerHTML = `${item['name']} (${item['chapters']})`;

                option.dataset.chapters = item['chapters'];
                option.dataset.abbrev = item['abbrev']['pt']
                
                select.appendChild(option)

            });


            for(let i = 0; i <= options.length; i++){
                let option = options[i];

                // selecionado o livro do parametro
                if(option.value == selectedBook){
                    option.selected = true;
                     // mostra quantia de capitulos
                     if(option.selected == true){
                        let selectChaptEl = document.createElement('select');
                        selectChaptEl.classList.add("form-control");
                        selectChaptEl.name = "chapter"

                        let numChapters = option.dataset.chapters
                        next.appendChild(selectChaptEl);


                        for(let chapter_i = 1; chapter_i <= numChapters; chapter_i++){
                            let chaptersEl = document.createElement('option');

                            chaptersEl.value = chapter_i
                            chaptersEl.dataset.number = chapter_i
                            chaptersEl.innerHTML = `${chapter_i}`

                            if(chaptersEl.value == selectedChapter){
                                chaptersEl.selected = true;
                            }

                            // verifica se o primeiro select carregou
                            if(document.readyState == 'complete'){
                                selectChaptEl.appendChild(chaptersEl);
                                changeBook();

                            }

                        } 
                        getBibleText(selectedChapter, numChapters);
                    }
                }else{
                    option.selected = false;
                }
              

                 // selecionado o livro do parametro

                if(option.value == selectedBook){
                    option.selected = true;                    
                }else{
                    option.selected = false;
                }

            }


        } // tudo certo
        
    });

    }

    function getBibleText(chapter, quantity) {
        let books = document.querySelectorAll('select[name=book] option')

        book = ''
        books.forEach(function(book){
            if(book.selected == true) {
                let url = `https://bible-api.com/${book.value}+${chapter}?translation=almeida`;

                let request = new Request(`${url}`, {
                    method: 'GET',
                });

                fetch(request).then(function(response) { return response.text() }).then(function(response){
                    response = JSON.parse(response)

                 
                    let verses = response['verses']
                    let i = 0;

                    verses.forEach(function(verse){
                        
                        verse['text'] = verse['text'].replaceAll('Deus', 'Yauh (יהוה)').replaceAll('deus', 'eterno').replaceAll('Senhor', 'criador').replaceAll('puro','limpo').replaceAll('misericórdia', 'benevolência').replaceAll(',]', ']').replaceAll('"','ˮ').replaceAll('\'','"').replaceAll('cruz','madeiro').replaceAll('Cruz', 'madeiro').replaceAll('amém', 'assim seja').replaceAll('batismo', 'imersão').replaceAll('glória', 'grandeza').replaceAll('vitória','conquista').replaceAll('misericordioso','benevolente').replaceAll('.','. ').replaceAll('!','! ').replaceAll('Amém','Assim seja').replaceAll('?', '? ').replaceAll('pausa','').replaceAll('Pausa', '').replaceAll('broquel', 'couraça').replaceAll('Senhor', 'eterno').replaceAll("senhor", "criador").replaceAll('todo-poderoso','onipotente').replaceAll('Todo-poderoso','onipotente').replaceAll('Todo-Poderoso','onipotente').replaceAll('laço do passarinho', 'laço do passarinheiro').replaceAll('Judá','Yauda').replaceAll('judá', 'yauda').replaceAll('David','Davi').replaceAll('Jesus','Yausha').replaceAll('JESUS','YAUSHA').replaceAll('Jesus Cristo','Yausha').replaceAll('Cristo','').replaceAll('EMANUEL','YAUSHA')

                        i++;
                        text.innerHTML += `<br><sub><a href="#versiculo_${i}" id="versiculo_${i}">${i}</a></sub>${verse['text']}`
                        
                    })

                    let sub = document.querySelectorAll("sub")
                    info.innerHTML += `<h3>| ${sub.length} versículos</h3>`
                    

                })

            }
        });
    }

    function changeBook(){
        try{
            let nextSelect = document.querySelector("#center > form > next > select")
            
            select.onchange = function(){
                let optionsNext = nextSelect.childNodes

                    optionsNext.forEach(function(optionNext){
                        if(optionNext.value == "1"){
                            optionNext.selected = true;
                        }else{
                            optionNext.selected = false;
                        }
                    })

                    submitBtn.click();  
            }

            nextSelect.onchange = function(){
                submitBtn.click();            
            }
        }catch(e){
            submitBtn.style.display = 'block';
        }
    }


    
    window.onload = function(){
        getBibleBooks();
        changeBook();
    }
    

    

</script>