<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\Date;

class Query
{
    static function filter(Builder $builder,
        Request $request, $columns = array()) {

        if(!$request->has('queryType'))
            return $builder;   

        if($request->input("queryType") === 'simple' &&
            $request->has('queryValue')) {
            return $builder->where(function ($subquery) use ($request){
                $subquery->whereRaw('LOWER(_filter) LIKE ?', [strtolower($request->input('queryValue'))."%"])
                    ->orWhereRaw('LOWER(_filter) LIKE ?', ["%".strtolower($request->input('queryValue'))."%"])
                    ->orWhereRaw('LOWER(_filter) LIKE ?', ["%".strtolower($request->input('queryValue'))]);
            });
        } 
        // Multiple filed search
        if($request->input("queryType") === 'advanced') {
            foreach ($columns as $fieldname => $column) {
                if($request->has('queryValue')) {
                    $queryValue = $request->input('queryValue');
                    if(array_key_exists($fieldname, $queryValue)) {
                        $value = $queryValue[$fieldname]["filterValue"];
                        $operator = false;
                        if(array_key_exists("operator", 
                            $queryValue[$fieldname]))
                            $operator = $queryValue[$fieldname]["operator"];

                        if($column["type"] === "flag" && !is_null($value)) {
                            $builder = $builder->where(
                                $column["columname"], "=", (bool) $value); 
                        }
                        if($operator === '~' && !is_null($value)) {
                            $builder = $builder->where(function ($subquery) use ($column, $value){
                                $subquery->whereRaw("LOWER(".$column["columname"].') LIKE ?', [strtolower($value)."%"])
                                    ->orWhereRaw("LOWER(".$column["columname"].')  LIKE ?', ["%".strtolower($value)."%"])
                                    ->orWhereRaw("LOWER(".$column["columname"].')  LIKE ?', ["%".strtolower($value)]);
                            });
                        }
                        if($operator === '=' && !is_null($value)) {
                           if($column["type"] === "mail") {
                                $builder = $builder->where(
                                    $column["columname"], "=", $value); 
                            }                            
                            if($column["type"] === "string") {
                                $builder = $builder->where(
                                    $column["columname"], "=", $value); 
                            }
                            if($column["type"] === "date") {
                                $builder = $builder->where(
                                    $column["columname"], "=", Date::W3c($value)); 
                            }  
                            if($column["type"] === "integer") {
                                $builder = $builder->where(
                                    $column["columname"], "=", (int) $value); 
                            }                        

                        } 
                        if($operator === '<' && !is_null($value)) {
                            if($column["type"] === "date") {
                                $builder = $builder->where(
                                    $column["columname"], "<=", Date::W3c($value)); 
                            }  
                            if($column["type"] === "integer") {
                                $builder = $builder->where(
                                    $column["columname"], "<=", (int) $value); 
                            }                        

                        }  
                        if($operator === '>' && !is_null($value)) {
                            if($column["type"] === "date") {
                                $builder = $builder->where(
                                    $column["columname"], ">=", Date::W3c($value)); 
                            }  
                            if($column["type"] === "integer") {
                                $builder = $builder->where(
                                    $column["columname"], ">=", (int) $value); 
                            }                        

                        }  
                        if($operator === '><' && !is_null($value)) {
                            $to = $queryValue[$column["columname"]]["filterValueTo"];
                            if($column["type"] === "date") {
                                $builder = $builder->whereBetween($column["columname"], 
                                    [Date::W3c($value), Date::W3c($to)]);
                            }  
                            if($column["type"] === "integer") {
                                $builder = $builder->whereBetween($column["columname"], 
                                    [$value, $to]);
                            }                        
                        } 


                    }

                }

            }
            return $builder;
        } 

        return $builder;

    }
}