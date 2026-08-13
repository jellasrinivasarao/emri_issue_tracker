<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FixAttachmentPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-attachment-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix attachment file paths that are missing issue_id in the path';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting attachment path fix...');
        
        // Get all attachments with incorrect paths
        $badAttachments = DB::table('txn_issue_attachment')
            ->whereRaw("file_path NOT LIKE CONCAT('%/issues/', issue_id, '/%')")
            ->get();
        
        $this->info("Found {$badAttachments->count()} attachments with incorrect paths");
        
        $fixed = 0;
        $notFound = 0;
        $errors = 0;
        
        foreach ($badAttachments as $attachment) {
            try {
                // Extract just the filename from the old path
                $fileName = basename($attachment->file_path);
                
                // Check if file exists in the issues directory
                $existingPath = "issues/{$fileName}";
                if (Storage::disk('public')->exists($existingPath)) {
                    // Move file to correct issue-specific directory
                    $newPath = "issues/{$attachment->issue_id}/{$fileName}";
                    
                    // Create directory if it doesn't exist
                    if (!Storage::disk('public')->exists("issues/{$attachment->issue_id}")) {
                        Storage::disk('public')->makeDirectory("issues/{$attachment->issue_id}");
                    }
                    
                    // Copy to new location
                    Storage::disk('public')->copy($existingPath, $newPath);
                    
                    // Update database with new path
                    DB::table('txn_issue_attachment')
                        ->where('attachment_id', $attachment->attachment_id)
                        ->update([
                            'file_path' => '/storage/' . $newPath
                        ]);
                    
                    $fixed++;
                    $this->line("✓ Fixed attachment {$attachment->attachment_id}");
                } else {
                    // File doesn't exist
                    $notFound++;
                    $this->warn("✗ Attachment {$attachment->attachment_id} file not found at {$existingPath}");
                    
                    // Mark as inactive so it doesn't cause errors
                    DB::table('txn_issue_attachment')
                        ->where('attachment_id', $attachment->attachment_id)
                        ->update(['is_active' => 0]);
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error processing attachment {$attachment->attachment_id}: {$e->getMessage()}");
            }
        }
        
        $this->info("\n=== Summary ===");
        $this->info("Fixed: {$fixed}");
        $this->warn("Not Found (marked inactive): {$notFound}");
        $this->error("Errors: {$errors}");
        
        return Command::SUCCESS;
    }
}
