<?php

namespace Ashmiass;

use Ashmiass\ApiDb;

class ApiHandler
{
    protected $db;
    
    public function __construct($conf)
    {
        $this->db =  new ApiDb($conf['connection']);
        $this->setContentType();
    }

    protected function setContentType(string $content_type = 'application/json')
    {
        header('Content-Type: ' . $content_type);
    }

    public function handleRequest($request, $request_method)
    {
        if ($request_method !== 'GET') {
            return;
        }

        $res = [];
        $action = 'ratings';
        if (key_exists('film', $request)) {
            $action = 'film';
        }
        if ($action == 'ratings') {
            $res = $this->executeRatingsAction($request);
        }
        if ($action == 'film' && !empty($request['film']) && is_numeric($request['film'])) {
            $res = $this->executeFilmAction($request);
        }
        return json_encode($res);
    }

    protected function executeFilmAction($request)
    {
        $film_id = $request['film'];
        return $this->db->getFilm($film_id);
    }

    protected function executeRatingsAction($request)
    {
        $today = new \DateTime();
        $date = $request['date']?? $today->format('Y-m-d');
        $sort = $request['sort']?? 'position';
        return $this->db->getRatings(['parsed_at' => $date, 'sort' => $sort]);
    }
}
