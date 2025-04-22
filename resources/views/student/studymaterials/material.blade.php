@extends('student.layouts.student')

@section('title', 'Student Dashboard')
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.css">
@section('css')
<style>
    :root {
      --parchment: #fff5e6;
      --ink: #2c1810;
      --leather: #8b4513;
      --gold: #d4af37;
    }
    
    /* body {
      background: #f0f0f0;
      margin: 0;
      padding: 20px;
      font-family: 'Georgia', serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    } */
    
    .document-card {
      width: 100%;
      max-width: 1200px;
      margin: 20px auto;
      background: var(--parchment);
      border: clamp(10px, 3vw, 20px) solid var(--leather);
      border-radius: 8px;
      box-shadow: 
        0 0 0 2px var(--gold),
        0 0 0 4px var(--leather),
        0 10px 20px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      position: relative;
      transition: all 0.3s ease;
    }
    
    /* Decorative corner flourishes */
    .document-card::before,
    .document-card::after {
      content: '';
      position: absolute;
      width: clamp(30px, 5vw, 60px);
      height: clamp(30px, 5vw, 60px);
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%238b4513'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z'%3E%3C/path%3E%3C/svg%3E");
      opacity: 0.2;
      pointer-events: none;
    }
    
    .document-card::before {
      top: 10px;
      left: 10px;
    }
    
    .document-card::after {
      bottom: 10px;
      right: 10px;
      transform: rotate(180deg);
    }
    
    .document-header {
      background: var(--leather);
      padding: clamp(15px, 2vw, 20px);
      border-bottom: 4px solid var(--gold);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      color: var(--parchment);
    }
    
    .document-title {
      font-size: clamp(18px, 3vw, 24px);
      font-weight: 600;
      margin: 0;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }
    
    .document-info {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .document-controls {
      display: flex;
      gap: 10px;
    }
    
    .control-button {
      background: var(--gold);
      color: var(--ink);
      border: 2px solid var(--ink);
      padding: 8px 15px;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 5px;
      font-weight: bold;
      white-space: nowrap;
    }
    
    .control-button:hover {
      background: var(--parchment);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .document-content {
      padding: clamp(15px, 3vw, 30px);
      min-height: 600px;
      position: relative;
      background-image: 
        linear-gradient(to right, rgba(139, 69, 19, 0.1) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(139, 69, 19, 0.1) 1px, transparent 1px);
      background-size: 20px 20px;
    }
    
    .document-viewer {
      width: 100%;
      height: 100%;
      border: 4px solid var(--leather);
      border-radius: 8px;
      background: var(--parchment);
    }
    
    /* Custom Scrollbar */
    .document-viewer::-webkit-scrollbar {
      width: clamp(8px, 1.5vw, 12px);
      height: clamp(8px, 1.5vw, 12px);
    }
    
    .document-viewer::-webkit-scrollbar-track {
      background: var(--parchment);
      border: 1px solid var(--leather);
      border-radius: 6px;
    }
    
    .document-viewer::-webkit-scrollbar-thumb {
      background: var(--leather);
      border: 2px solid var(--gold);
      border-radius: 6px;
      box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
    }
    
    .document-viewer::-webkit-scrollbar-thumb:hover {
      background: var(--ink);
    }
    
    .document-viewer::-webkit-scrollbar-corner {
      background: var(--parchment);
    }
    
    /* Fullscreen styles */
    .fullscreen {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 9999;
      margin: 0;
      border-width: 0;
      border-radius: 0;
    }
    
    .fullscreen .document-content {
      height: calc(100vh - 85px);
      padding: 15px;
    }
    
    .fullscreen .document-viewer {
      height: 100%;
    }
    
    /* Decorative elements */
    .page-corner {
      position: absolute;
      width: clamp(30px, 4vw, 50px);
      height: clamp(30px, 4vw, 50px);
      background: linear-gradient(135deg, var(--parchment) 50%, transparent 50%);
      bottom: 0;
      right: 0;
      transform-origin: bottom right;
      transition: transform 0.3s ease;
    }
    
    .bookmark {
      position: absolute;
      top: -10px;
      right: clamp(50px, 10vw, 100px);
      width: clamp(20px, 3vw, 30px);
      height: clamp(60px, 8vw, 80px);
      background: var(--gold);
      border-radius: 0 0 4px 4px;
      box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
      display: none;
    }
    
    @media (min-width: 768px) {
      .bookmark {
        display: block;
      }
    }
    
    .file-type {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      background: var(--gold);
      border-radius: 4px;
      font-size: clamp(14px, 2vw, 16px);
      color: var(--ink);
      border: 2px solid var(--ink);
    }
    
    .file-type::before {
      content: '';
      display: inline-block;
      width: clamp(16px, 2.5vw, 20px);
      height: clamp(16px, 2.5vw, 20px);
      margin-right: 8px;
      background-size: contain;
    }
    
    .file-type.pdf::before {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 384 512'%3E%3Cpath fill='%232c1810' d='M181.9 256.1c-5-16-4.9-46.9-2-46.9 8.4 0 7.6 36.9 2 46.9zm-1.7 47.2c-7.7 20.2-17.3 43.3-28.4 62.7 18.3-7 39-17.2 62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1 428.1c0 .8 13.2-5.4 34.9-40.2-6.7 6.3-29.1 24.5-34.9 40.2zM248 160h136v328c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V24C0 10.7 10.7 0 24 0h200v136c0 13.2 10.8 24 24 24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8 4.5-18.5 11.6-46.6 6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5 18.3-.4 44.1 8.1 77-11.6 27.6-28.7 64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1 13.9-73.6 44.5-54.5 68 5.6 6.9 16 10 21.5 10 17.9 0 35.7-18 61.1-61.8 25.8-8.5 54.1-19.1 79-23.2 21.7 11.8 47.1 19.5 64 19.5 29.2 0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377 105L279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1 255.3c4.1-2.7-2.5-11.9-42.8-9 37.1 15.8 42.8 9 42.8 9z'/%3E%3C/svg%3E");
    }
    
    /* Animations */
    @keyframes pageTurn {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(-5deg); }
    }
    
    .document-card:hover .page-corner {
      animation: pageTurn 1s ease-in-out infinite alternate;
    }
    
    @keyframes bookmarkSway {
      0% { transform: rotate(0deg); }
      50% { transform: rotate(5deg); }
      100% { transform: rotate(0deg); }
    }
    
    .bookmark:hover {
      animation: bookmarkSway 2s ease-in-out infinite;
    }
    
    /* Media Queries */
    @media (max-width: 480px) {
      body {
        padding: 10px;
      }
      
      .document-header {
        flex-direction: column;
        text-align: center;
      }
      
      .document-info {
        justify-content: center;
      }
      
      .document-controls {
        width: 100%;
        justify-content: center;
      }
      
      .control-button {
        padding: 6px 12px;
        font-size: 14px;
      }
    }
    
    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
      body {
        background: #2c2c2c;
      }
      
      .document-card {
        box-shadow: 
          0 0 0 2px var(--gold),
          0 0 0 4px var(--leather),
          0 10px 30px rgba(0, 0, 0, 0.5);
      }
    }
    </style>
@endsection
@section('student')



<div class="study-material-viewer">
    <div class="viewer-header">
        <h2 class="text-white">{{ $studyMaterial->lecture_name }}</h2>
        <div class="course-info text-white">
            <span>Course: {{ $studyMaterial->course->title }}</span>
        </div>
    </div>
    
    <div class="document-card" id="documentCard">
        <div class="document-header">
          <div class="document-info">
            <span class="file-type pdf">Preview study materials</span>
          </div>
        
        </div>
        <div class="bookmark"></div>
        <div class="document-content">
    
            <iframe class="document-viewer" src="{{ url('student/studymaterials/view-file/' . $fileUrl) }}" frameborder="0"></iframe>
            <div class="page-corner"></div>
            
          </div>
      </div>
</div>
<script>
    const documentCard = document.getElementById('documentCard');
    const toggleButton = document.getElementById('toggleView');
    const fileInput = document.getElementById('fileInput');
    const uploadContainer = document.getElementById('uploadContainer');
    const filePreview = document.getElementById('filePreview');
    const documentViewer = document.getElementById('documentViewer');
    
    // File upload handling
    fileInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const fileURL = URL.createObjectURL(file);
        
        // Update title with file name
        document.querySelector('.document-title').textContent = file.name;
        
        // Update file type indicator
        const fileTypeIndicator = document.querySelector('.file-type');
        fileTypeIndicator.textContent = file.name.endsWith('.pdf') ? 'PDF Document' : 'Word Document';
        fileTypeIndicator.className = `file-type ${file.name.endsWith('.pdf') ? 'pdf' : 'docx'}`;
        
        // Show preview
        filePreview.style.display = 'block';
        filePreview.data = fileURL;
        uploadContainer.classList.add('hidden');
        documentViewer.classList.remove('empty');
      }
    });
    
    // Fullscreen handling
    toggleButton.addEventListener('click', () => {
      documentCard.classList.toggle('fullscreen');
      const isFullscreen = documentCard.classList.contains('fullscreen');
      toggleButton.textContent = isFullscreen ? 'Close Book' : 'Open Book';
      
      if (document.fullscreenElement) {
        document.exitFullscreen();
      } else {
        documentCard.requestFullscreen();
      }
    });
    
    // Handle fullscreen change events
    document.addEventListener('fullscreenchange', () => {
      if (!document.fullscreenElement) {
        documentCard.classList.remove('fullscreen');
        toggleButton.textContent = 'Open Book';
      }
    });
    
    // Escape key handler
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && documentCard.classList.contains('fullscreen')) {
        documentCard.classList.remove('fullscreen');
        toggleButton.textContent = 'Open Book';
      }
    });
    </script>
@endsection