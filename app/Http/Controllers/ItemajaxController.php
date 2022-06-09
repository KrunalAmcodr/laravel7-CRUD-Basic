<?php

namespace App\Http\Controllers;

use App\Itemajax;
use Illuminate\Http\Request;
use DataTables;

class ItemajaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $items_array = Itemajax::all();
        // return view('ajaxitems.ajaxitems', compact('items_array'));

        if ($request->ajax()) {
            $ajaxitems = Itemajax::all();
            return datatables()->of($ajaxitems)->addIndexColumn()->escapeColumns([])
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-info btn-edit" id="editajaxitem" data-rowid="' . $row->id . '" data-routeurl="'. route('ajaxitems.update', $row->id) .'">Edit</button> ';
                    $html .= '<button class="btn btn-primary btn-view" data-rowid="' . $row->id . '" id="viewajaxitem" data-routeurl="'. route('ajaxitems.show', $row->id) .'">View</button> ';
                    $html .= '<button data-rowid="' . $row->id . '" class="btn btn-danger btn-delete" id="deleteajaxitem" data-routeurl="'. route('ajaxitems.destroy', $row->id) .'">Delete</button>';
                    return $html;
                })->toJson();
        }

        return view('ajaxitems.ajaxitems');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!empty($request->id)){
            if($request->hasfile('images')){
                $request->validate([
                    'item_name' => 'required',
                    'descriptions' => 'required',
                    'manufacture_date' => 'required',
                    'images' => 'required',
                    'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                ]);
            } else {
                $request->validate([
                    'item_name' => 'required',
                    'descriptions' => 'required',
                    'manufacture_date' => 'required',
                ]);
            }
        } else {
            $request->validate([
                'item_name' => 'required',
                'descriptions' => 'required',
                'manufacture_date' => 'required',
                'images' => 'required',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        }
        
        if($request->hasfile('images')){
            foreach($request->file('images') as $image){
                $name = $image->getClientOriginalName();
                $image->move(public_path() . '/image/', $name);
                $images_data[] = $name;
            }
        }

        if($request->hasfile('images')){
            $Item_create = Itemajax::updateOrCreate(
                ['id' => $request->id],
                [
                'item_name' => $request->item_name,
                'descriptions' => $request->descriptions,
                'manufacture_date' => date("Y-m-d", strtotime($request->manufacture_date)),
                'images' => json_encode($images_data)
            ]);
        } else {
            $Item_create = Itemajax::updateOrCreate(
                ['id' => $request->id],
                [
                'item_name' => $request->item_name,
                'descriptions' => $request->descriptions,
                'manufacture_date' => date("Y-m-d", strtotime($request->manufacture_date))
            ]);
        }

        return response()->json(['code'=>200, 'message'=>'Item created successfully.','data' => $Item_create], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        echo "<pre>";
        print_r($request->all());
        exit();
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Itemajax::find($id)->delete();
     
        return response()->json(['code'=>200, 'message'=>'Item deleted successfully.'], 200);
    }
}
