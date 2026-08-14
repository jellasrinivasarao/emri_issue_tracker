<?php

namespace Tests\Feature;

use App\Http\Controllers\IssueController;
use App\Models\Issue;
use App\Models\IssueAttachment;
use App\Services\IssueRoutingService;
use App\Services\IssueService;
use App\Services\IssueWorkflowService;
use Tests\TestCase;

class IssueAttachmentFormattingTest extends TestCase
{
    public function test_format_issue_includes_attachments(): void
    {
        $issue = Issue::make([
            'issue_id' => 101,
            'issue_number' => 'IS-20260813105',
            'issue_title' => 'Attachment issue',
            'issue_description' => 'Issue description',
            'state' => 'Open',
            'service_id' => 1,
            'project_id' => 2,
            'application_id' => 3,
            'module_id' => 4,
            'priority_id' => 5,
            'status_id' => 6,
        ]);

        $attachment = new IssueAttachment();
        $attachment->setAttribute('attachment_id', 12);
        $attachment->setAttribute('issue_id', 101);
        $attachment->setAttribute('original_file_name', 'report.pdf');
        $attachment->setAttribute('stored_file_name', 'report.pdf');
        $attachment->setAttribute('file_path', '/storage/issues/101/report.pdf');
        $attachment->setAttribute('file_size', 123);
        $attachment->setAttribute('file_type', 'application/pdf');
        $attachment->setAttribute('uploaded_at', '2026-08-13 10:00:00');

        $issue->setRelation('attachments', collect([$attachment]));

        $controller = new IssueController(
            app(IssueService::class),
            app(IssueRoutingService::class),
            app(IssueWorkflowService::class)
        );

        $method = new \ReflectionMethod(IssueController::class, 'formatIssue');
        $method->setAccessible(true);

        $result = $method->invoke($controller, $issue);

        $this->assertArrayHasKey('attachments', $result);
        $this->assertCount(1, $result['attachments']);
        $this->assertSame(12, $result['attachments'][0]['attachment_id']);
    }
}
