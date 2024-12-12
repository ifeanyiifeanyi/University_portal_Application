<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Ticket Response</title>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }
            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }

        /* Base styles */
        body {
            background-color: #f8fafc;
            color: #2d3748;
            height: 100%;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        .wrapper {
            background-color: #f8fafc;
            margin: 0;
            padding: 50px;
            width: 100%;
        }

        .inner-body {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            padding: 0;
            width: 570px;
        }

        /* Header section */
        .header {
            padding: 25px;
            text-align: center;
            background: #1a56db;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }

        /* Content section */
        .content {
            background: #ffffff;
            padding: 35px;
        }

        .ticket-info {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .ticket-info p {
            margin: 5px 0;
            color: #4a5568;
        }

        .qa-pair {
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .question {
            background: #f3f4f6;
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .question h3 {
            margin: 0;
            color: #1a56db;
            font-size: 16px;
        }

        .response {
            background: #ffffff;
            padding: 16px;
            white-space: pre-line;
        }

        .response-header {
            margin-bottom: 10px;
            color: #4a5568;
            font-size: 14px;
        }

        .reply-info {
            background: #f8fafc;
            padding: 12px 16px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #4a5568;
        }

        /* Footer section */
        .footer {
            margin: 0 auto;
            padding: 20px;
            text-align: center;
            width: 570px;
        }

        .footer p {
            color: #6b7280;
            font-size: 12px;
            text-align: center;
        }

        .reply-button {
            display: inline-block;
            background: #1a56db;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="inner-body">
            <div class="header">
                <h1>Support Ticket Response</h1>
            </div>

            <div class="content">
                <div class="ticket-info">
                    <p><strong>Ticket #:</strong> {{ $ticketNumber }}</p>
                    <p><strong>Subject:</strong> {{ $subject }}</p>
                    <p><strong>Department:</strong> {{ $department }}</p>
                    <p><strong>Status:</strong> {{ Str::replace('_', ' ', ucfirst($ticket->status)) }}</p>
                </div>

                @foreach($responses as $response)
                    <div class="qa-pair">
                        <div class="question">
                            <h3>Question {{ $loop->iteration }}:</h3>
                            <p>{{ $response->question->question }}</p>
                        </div>
                        <div class="response">
                            <div class="response-header">
                                <strong>Response from {{ $response->admin->user->full_name }}:</strong>
                                <br>
                                <small>{{ $response->created_at->format('F j, Y, g:i a') }}</small>
                            </div>
                            {!! nl2br(e($response->response)) !!}
                        </div>
                        <div class="reply-info">
                            <p>To reply to this specific response, please include "Re: Question {{ $loop->iteration }}" in your email subject when responding.</p>
                        </div>
                    </div>
                @endforeach

                <div style="margin-top: 30px;">
                    <p><strong>Best regards,</strong></p>
                    <p>{{ $respondentName }}</p>
                    <p>{{ $department }} Department</p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>You can reply to this email directly to continue the conversation.</p>
            <p>Each response includes a reference to the specific question it addresses.</p>
            <p>For a complete overview of your ticket, visit the support portal.</p>
        </div>
    </div>
</body>
</html>
