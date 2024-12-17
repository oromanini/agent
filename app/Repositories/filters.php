<?php

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;

function filterName($data, $query)
{
    if (!empty($data['name_filter'])) {
        $model = $query->getModel();

        if ($model instanceof Proposal) {
            return $query->whereRelation('client', 'name', 'like', '%' . $data['name_filter'] . '%');
        } else {
            return $query->where('name', 'like', '%' . $data['name_filter'] . '%');
        }
    }
}

function filterDocument($data, $query)
{
    if (!empty($data['document_filter'])) {
        $model = $query->getModel();

        if ($model instanceof Proposal) {
            return $query->whereRelation('client', 'document', 'like', '%' . $data['document_filter'] . '%');
        } else {
            return $query->where('document', 'like', '%' . $data['document_filter'] . '%');
        }
    }
}

function filterUser($data, $query) {
    if (!Auth::user()->is_admin) {
        return $query->where('agent_id', Auth::user()->id);
    }
}

function filterCnpj($data, $query)
{
    if (!empty($data['cnpj_filter'])) {
        return $query->where('cnpj', 'like', '%' . $data['cnpj_filter'] . '%');
    }
}

function filterPhoneNumber($data, $query)
{
    if (!empty($data['phone_number_filter'])) {
        return $query->where('phone_number', 'like', '%' . $data['phone_number_filter'] . '%');
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
