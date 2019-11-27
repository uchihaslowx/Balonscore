<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompetitionController extends Controller
{

    private $url = 'https://api.football-data.org/v2/competitions/';
    private $authorization = "x-auth-token: a5c86047e9c246799d1de33fca2037be";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function list()
    {
        $curl_session = curl_init($this->url);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $this->authorization));
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl_session);
        curl_close($curl_session);
        $data = json_decode($result, true);

        for ($i = 0; $i < count($data['competitions']); $i++) {
            $array[] = $data['competitions'][$i];
        }

        return $array;
    }

    public function findById($id)
    {
        $ch = curl_init($this->url . $id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $this->authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $value = json_decode($result, true);
        $this->saveTeams($id);
        return $value;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * metodos auxiliares
     */

    private function saveTeams($id)
    {
        try
        {
            $curlsession = curl_init($this->url . $id . '/teams');
            curl_setopt($curlsession, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $this->authorization));
            curl_setopt($curlsession, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($curlsession);

            $lastHttpCode = curl_getinfo($curlsession, CURLINFO_HTTP_CODE);

            switch ($lastHttpCode) {
                case 400:
                    throw new Exception('Bad Request');
                    break;
                case 403:
                    throw new Exception('Restricted Resource');
                    break;
                case 404:
                    throw new Exception('Not Found');
                    break;
                case 429:
                    throw new Exception('Too Many Requests');
                    break;
                default:
                    throw new Exception('Error inesperado');
            }

            curl_close($curlsession);
            $data = json_decode($result, true);

            for ($i = 0; $i < $data['count']; $i++) {
                $validate = Team::find($data['teams'][$i]['id']);
                if ($validate == null) {
                    $team = new  Team();
                    $team->codigo = $data['teams'][$i]['id'];
                    $team->name = $data['teams'][$i]['name'];
                    DB::transaction(function () use ($data, $team)
                    {
                        $team->save();
                    });
                }
            }

            $this->players();

            $r['mensaje'] = 'OK';
            $r['datos'] = $data;

        } catch (Exception $exception)
        {
            $r['mensaje'] = $exception->getMessage();
        }

        return $r;
    }
}
