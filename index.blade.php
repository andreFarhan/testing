@extends('layouts.default')
@section('header')
    <style>
        .dataTables_wrapper .dataTables_length select {
            width: 65px;
        }

        .filter {
            display: table-header-group;
        }

        table.dataTable tfoot.filter th {
            padding-top: 0px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.3);
            border-top: 0px;
        }

        table.dataTable thead#head th {
            border-bottom: 0px;
            border-top: 1px solid rgba(0, 0, 0, 0.3);
        }

        .select2-selection__rendered li {
            margin: 6px 0px 0px 4px !important;
        }

        .dataTables_length {
            margin-bottom: 10px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
@endsection
@section('content')
    <h2 class="intro-y text-lg font-medium mt-10">
        Role Manajemen
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table id="table_roles" class="table">
                <thead id="head">
                    <tr>
                        <th>NAMA</th>
                        <th>EMAIL</th>
                        <th>NO. HANDPHONE</th>
                        <th>ROLES</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                        <th>LAST MODIFIED</th>
                    </tr>
                </thead>
                <tfoot class="filter">
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div id="test">

    </div>
@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('#tap-role-manajemen').addClass('side-menu--active');
        const limit = 10
        const page = 1
        const column = ['nama', 'email', 'nomor_telp', 'roles', 'status'];

        function debounce(func, wait, immediate) {
            let timeout;
            return function() {
                const context = this,
                    args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };

        const table = $('#table_roles').DataTable({
            serverSide: true,
            processing: true,
            deferRender: true,
            autoWidth: false,
            dom: `
                <
                    <"util grid grid-cols-12 gap-6 mt-5"<"col-span-6"l>>
                    <tr>
                    <
                        <i><p>
                    >
                >`,
            ajax: {
                url: "{{ route('getDataRoles') }}",
                type: 'GET',
                data: function(d) {
                    d.limit = d.length;
                    d.page = $('#table_roles').DataTable().page() + 1;
                    d.nama = d.columns[1].search.value;
                    d.email = d.columns[2].search.value;
                    d.nomor_telp = d.columns[3].search.value;
                    d.roles = d.columns[4].search.value;
                    d.status = d.columns[5].search.value;
                    d.column = column[$('#table_roles').DataTable().order().length ? $('#table_roles')
                        .DataTable()
                        .order()[0][0] : 0];
                    d.order = $('#table_roles').DataTable().order().length ? $('#table_roles').DataTable()
                        .order()[0][
                            1
                        ] : 'asc';
                    // console.log(d);
                },
                dataSrc: function(json) {
                    json.recordsTotal = json.data.total;
                    json.recordsFiltered = json.data.total;
                    return json.data.data;
                }
            },
            columns: [{
                    render: function(data) {
                        return data;
                    },
                },
                {
                    data: 'nama'
                },
                {
                    data: 'email'
                },
                {
                    data: 'nomor_telp'
                },
                {
                    data: 'roles'
                },
                {
                    data: 'status'
                },
                {
                    data: null,
                    render: function() {
                        return `
                            <div class="dropdown">
                                <button class="dropdown-toggle btn btn-primary btn-sm" aria-expanded="false" data-tw-toggle="dropdown">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                <div class="dropdown-menu w-40">
                                    <ul class="dropdown-content">
                                        <li> <a href="" class="dropdown-item">Edit Data</a> </li>
                                        <li> <a href="" class="dropdown-item">Hapus Data</a> </li>
                                    </ul>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: null,
                    "render": function(data, type, row, meta) {
                        var updatedAt = moment(row.updated.at).format('MMM DD, YYYY @ HH:mm');
                        return updatedAt + ' by ' + row.updated.by;
                    }
                }
            ],
            order: [
                [7, 'asc']
            ],
            columnDefs: [{
                    orderable: false,
                    targets: [0, 6]
                },
                {
                    targets: '_all',
                    className: 'text-center'
                }
            ],
            initComplete: function() {
                this.api().columns([1, 2, 3, 4, 5]).every(function(colIdx) {
                    const column = this;
                    const input = `
                            <input type="text" class="form-control">
                        `;

                    $(input).appendTo($(column.footer()).empty()).on('keyup', debounce(function() {
                        const val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? val : '', true, false).draw();
                    }, 500));
                });
            }
        });
    </script>
@endsection
