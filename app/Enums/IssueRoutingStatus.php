<?php

namespace App\Enums;

enum IssueRoutingStatus: string
{
    case PENDING = 'PENDING';
    case ROUTED = 'ROUTED';
    case WAITING_BUSINESS_HOURS = 'WAITING_BUSINESS_HOURS';
    case ESCALATED = 'ESCALATED';
    case FAILED = 'FAILED';
}