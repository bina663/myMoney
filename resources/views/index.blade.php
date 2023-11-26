
<?php
    if(isset($_SESSION["spents"])){
        $spents = $_SESSION["spents"];
    }
    if(isset($_SESSION["balance"])){
        $balance = $_SESSION["balance"];
    }
    if(isset($_SESSION["total"])){
        $total = $_SESSION["total"];
    }
    $balance = (empty($balance) ? "0.00" : $balance);
    $total = (empty($total) ? "0.00" : $total);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <title>My Money</title>
</head>
<body>
    
    <nav class="teal darken-2">
        <div class="nav-wrapper">
          <a href="#" class="brand-logo center">My Money</a>
        </div>
      </nav>
    {{-- Balance --}}
    <div class="container">
            {{-- Balance e Spent --}}
            <div class="row center-align">
                <div class="row">
                    <div class="col s6">
                        <h4 id="text-balance">Balance: {{$balance}}</h4>
                    </div>
                    <div class="col s6">
                        <h4>Spent: {{$total}}</h4>
                    </div>
                </div>
             </div>
            {{-- Add balance --}}
            <div class="section">
                <div class="row">
                    <form action="/save" method="POST" class="col s12" id="form-save">
                        @csrf
                        <div class="row">
                            <div class="input-field col s6">
                                <input type="text" placeholder="{{$balance}}" id="balance" class="value validate" name="balance" required>
                                <label for="balance">Balance</label>
                                @if(!empty($balance) && $balance != "0.00")
                                    <button class="waver-effect waver-light btn" id="save">Add balance</button>
                                    <div class="waver-effect yellow accent-4 btn" id="edit">Edit balance</div>
                                @else
                                    <button class="waver-effect waver-light btn" id="save">Save balance</button>
                                @endif
                                
                            </div>   
                        </div><!-- row -->
                    </form>
                </div><!-- row -->
            </div>
            {{-- Add Spent --}}
            <div class="row">
                <div class="row">
                    <div class="col s12" id="form">
                        <div class="section" style="margin-bottom:10px;">
                            <button class="waves-effect waves-light btn" id="addSpent">Add Spent</button>
                            <a href="/deleteAll" class="red lighten-2 btn" id="addSpent">Delete all</a>
                        </div>
                        <div class="row div-form" id="div-form">
                            <form method="POST" action="/save" class="col s12">
                                @csrf
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input type="text" placeholder="" name="name" id="name" class="validate">
                                        <label for="name">Name Spent</label>
                                    </div>
                                </div><!-- name -->
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input type="text" placeholder="" name="value" id="value" class="value validate">
                                        <label for="value">Value</label>
                                    </div>  
                                </div><!-- value -->
                                <button class="waves-effect waves-light btn" id="saveSpent">Save Spent</button>
                            </form>
                        </div><!-- row -->  
                    </div>
                </div>
            </div>
            {{-- All Spents --}}
            <div class="row">
                @if(isset($spents))
                    <div class="row">
                        <div class="col s12">
                            <table class="striped centered responsive-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                @if (isset($spents))
                                    @for ($i = 0; $i < count($spents); $i++)
                                        <tr>
                                            <td>{{($spents[$i]["name"]) }}</td>
                                            <td>{{($spents[$i]["value"]) }}</td>
                                            <td>
                                                <form method="POST" action="/delete/{{ $i }}">
                                                    @csrf
                                                    <button class="waves-effect waves-light btn-small red lighten-1" type="submit"><i class="material-icons left">delete</i>Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endfor
                                @endif
                            </table>
                            <div class="section">
                                <div class="row">
                                    <div class="col s12">
                                        <form action="/import" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="file-field input-field">
                                                <div class="btn">
                                                    <span>File</span>
                                                    <input type="file" name="file">
                                                </div>
                                                <div class="file-path-wrapper">
                                                    <input class="file-path validate" type="text">
                                                </div>
                                                <button class="waves-effect waves-light btn">import</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col s12">
                                        <a  href="/export" class="waves-effect waves-light btn"><i class="material-icons left">arrow_downward</i>Export</a>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                @endif
            </div>
    </div><!-- container -->
    </div>
    <script>
        /**
         * Just get active form
        */
        const addSpentEl = document.querySelector("#addSpent");
        const divFormEl = document.querySelector("#div-form");
        divFormEl.style.display = "none";
        addSpentEl.addEventListener("click", () => {
            if (divFormEl.style.display !== "block") {
                addSpentEl.innerHTML = "Cancel";
                divFormEl.style.display = "block";
            } else {
                addSpentEl.innerHTML = "Add Spent";
                divFormEl.style.display = "none";
            }
        });

        /**
         * Convert value in float
         */
        const valueEl = document.querySelectorAll(".value");
        valueEl.forEach((el) => {
            el.addEventListener("blur", () => {
                const parsedValue = parseFloat(el.value);
                if (isNaN(parsedValue))
                {
                    el.placeholder = "0.00"
                }else{
                    el.value = parseFloat(el.value).toFixed(2);
                }
            });
        });

        const editEl = document.querySelector("#edit");
        editEl.addEventListener("click", () => {

            const saveEl = document.querySelector("#save");
            const inputEl = document.querySelector("#balance");
            const balanceEl = document.querySelector("#text-balance");
            const formSaveEl = document.querySelector("#form-save");
            if(editEl.innerHTML == "Cancel"){
                editEl.innerHTML = "Edit Balance";
                editEl.classList.remove("red", "lighten-2");
                editEl.classList.add("yellow", "accent-4");
                inputEl.name = "balance";
                saveEl.innerHTML = "Add balance";
                formSaveEl.action = "/save";
            }else{
                editEl.innerHTML = "Cancel";
                editEl.classList.remove("yellow", "accent-4");
                editEl.classList.add("red", "lighten-2");
                saveEl.innerHTML = "Save Change";
                const valueBalance = balanceEl.innerHTML.split(" ");
                inputEl.name = "editBalance";
                inputEl.placeholder = valueBalance[1];
                formSaveEl.action = "/edit";
            }

        });
    </script>
</body>
</html>