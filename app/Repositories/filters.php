<?php

use Illuminate\Support\Facades\Auth;

function filterName($data, $query)
{
    if (!empty($data['name_filter'])) {
        return $query->where('name', 'like', '%' . $data['name_filter'] . '%');
    }
}

function filterDocument($data, $query)
{
    if (!empty($data['document_filter'])) {
        return $query->where('document', 'like', '%' . $data['document_filter'] . '%');
    }
}

function filterAgent($data, $query)
{
    if (!empty($data['agent_filter']) && $data['agent_filter'] != 0) {
        return $query->where('agent_id', $data['agent_filter']);
    }
}

function filterInitialDate($data, $query)
{
    if (!empty($data['initial_date_filter'])) {
        return $query->where('created_at', '>=', new \DateTime($data['initial_date_filter']));
    }
}

function filterFinalDate($data, $query)
{
    if (!empty($data['final_date_filter'])) {
        $query->where('created_at', '<=', new \DateTime($data['final_date_filter']));
    }
}


