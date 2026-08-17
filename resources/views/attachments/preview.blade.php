<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $attachment->original_file_name }} - Preview</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f3f4f6;
        }
        
        .container {
            max-width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            word-break: break-word;
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2563eb;
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #1f2937;
        }
        
        .btn-secondary:hover {
            background: #d1d5db;
        }
        
        .content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: auto;
        }
        
        .preview-area {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .preview-pdf {
            width: 100%;
            height: 100%;
        }
        
        .preview-text {
            width: 100%;
            height: 100%;
            padding: 2rem;
            overflow: auto;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
            color: #374151;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .preview-office {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .preview-not-supported {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
        
        .preview-not-supported-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .preview-not-supported-text {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-title">
                📄 {{ $attachment->original_file_name }}
            </div>
            <div class="header-actions">
                <a href="{{ $downloadUrl }}" class="btn btn-primary" download>
                    ↓ Download
                </a>
                <button onclick="window.close()" class="btn btn-secondary">
                    Close
                </button>
            </div>
        </div>
        
        <div class="content">
            <div class="preview-area">
                @if ($isImage)
                    <img src="{{ $publicUrl }}" alt="{{ $attachment->original_file_name }}" class="preview-image">
                
                @elseif ($isPdf)
                    <embed src="{{ $publicUrl }}" type="application/pdf" class="preview-pdf" />
                
                @elseif ($isText)
                    <div class="preview-text">{{ $content }}</div>
                
                @elseif ($isOffice)
                    <iframe src="https://docs.google.com/gview?url={{ urlencode($publicUrl) }}&embedded=true" class="preview-office"></iframe>
                
                @else
                    <div class="preview-not-supported">
                        <div class="preview-not-supported-icon">
                            📋
                        </div>
                        <div class="preview-not-supported-text">
                            <strong>Preview not available</strong><br>
                            <small>This file type cannot be previewed in the browser</small>
                        </div>
                        <a href="{{ $downloadUrl }}" class="btn btn-primary" download>
                            Download to view
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
