<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BaseData\Bd;
class EventContoller extends Controller
{
    private $bd;
    public function __construct()
    {
        session_start();
        $this->bd = new Bd();
    }
    public function index(){
        return view("index");
    }
    public function save(Request $request){

        if(isset($request["balance"])){
            $this->bd::save($request["balance"]);
            return redirect("/");
        }
        $name = $request["name"];
        $value = $request["value"];
        if(empty($name) or empty($value)){
            return redirect("/")->with("message", "Form Empty");
        }
        $bd = new Bd($name, $value);
        $bd::save();
        return redirect("/");
    }
    public function deleteAll(){ 
        $this->bd::deleteAll();
        return redirect("/");
    }
    public function delete(Request $request){
        $this->bd::delete($request["id"]);
        return redirect("/");
    }
    public function export(){
        $this->bd::export();
        return redirect("/");
    }
    public function edit(Request $request){
        if($request["editBalance"]){
            $this->bd::edit($request["editBalance"]);
        }
        
        return redirect("/");
    }
    public function import(Request $request){
        $this->bd::import($request["file"]);
        die;
    }
}
