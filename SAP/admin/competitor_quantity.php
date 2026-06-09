<!DOCTYPE html>
<html>
<head>
    <title>Upload Competitor CSV</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #eef1f5;
            color: #243447;
        }

        .page-wrap {
            width: 70%;
            max-width: 900px;
            margin: 48px auto;
        }

        .message-panel {
            margin-bottom: 18px;
            text-align: center;
            color: #c62828;
            font-weight: bold;
            min-height: 24px;
        }

        .upload-card {
            background: #ffffff;
            border: 1px solid #cfd8e3;
            box-shadow: 0 10px 28px rgba(36, 52, 71, 0.08);
        }

        .card-head {
            padding: 14px 18px;
            background: linear-gradient(90deg, #1f4e79, #2f6aa3);
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
        }

        .card-body {
            padding: 24px 28px 28px;
        }

        table.form-table {
            width: 100%;
            border-collapse: collapse;
        }

        .form-table td {
            padding: 10px 6px;
            vertical-align: middle;
        }

        .label-cell {
            width: 180px;
            text-align: right;
            font-weight: bold;
            color: #334e68;
        }

        .colon-cell {
            width: 16px;
            text-align: center;
            color: #334e68;
        }

        input[type="file"] {
            width: 100%;
            max-width: 360px;
            padding: 10px;
            border: 1px solid #c5d0db;
            background: #f8fafc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .note {
            display: block;
            margin-top: 8px;
            color: #c62828;
            font-weight: bold;
            font-size: 13px;
        }

        .actions {
            padding-top: 8px;
        }

        button {
            min-width: 110px;
            padding: 10px 18px;
            margin-right: 10px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .primary-btn {
            background: #1f4e79;
            color: #ffffff;
        }

        .secondary-btn {
            background: #e6eef7;
            color: #1f4e79;
            border: 1px solid #b8cbe0;
        }

        .status-message {
            margin-top: 18px;
            padding: 12px 14px;
            border: 1px solid #d7e1eb;
            background: #f8fafc;
            color: #1f2933;
            min-height: 20px;
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .page-wrap {
                width: calc(100% - 24px);
                margin: 24px auto;
            }

            .card-body {
                padding: 18px;
            }

            .form-table,
            .form-table tbody,
            .form-table tr,
            .form-table td {
                display: block;
                width: 100%;
            }

            .label-cell {
                text-align: left;
                padding-bottom: 4px;
            }

            .colon-cell {
                display: none;
            }

            button {
                width: 100%;
                margin: 0 0 10px;
            }
        }
    </style>
</head>
<body>
<?php require("adminUtils.php"); ?>
<div class="page-wrap">
    <div class="message-panel" id="topMsg"></div>

    <div class="upload-card">
        <div class="card-head">Upload Competitor CSV</div>

        <div class="card-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <table class="form-table">
                    <tr>
                        <td class="label-cell">CSV File*</td>
                        <td class="colon-cell">:</td>
                        <td>
                            <input type="file" name="csv_file" accept=".csv" required>
                            <span class="note">[Extension will be .csv]</span>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="colon-cell">&nbsp;</td>
                        <td class="actions">
                            <button type="submit" class="primary-btn">Upload</button>
                            <button type="button" class="secondary-btn" onclick="downloadCSV()">Download Sample CSV</button>
                        </td>
                    </tr>
                </table>
            </form>

            <div class="status-message" id="msg"></div>
        </div>
    </div>
</div>

<script>
document.getElementById("uploadForm").onsubmit = function(e){
    e.preventDefault();

    var formData = new FormData(this);

    fetch("upload_csv.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("msg").innerHTML = data.message;
        document.getElementById("topMsg").innerHTML = data.message;
    });
};

function downloadCSV(){
    window.location.href = "download_sample.php";
}
</script>

</body>
</html>
