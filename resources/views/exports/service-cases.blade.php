<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Company</th>
            <th>Staff</th>
            <th>Service</th>
            <th>Status</th>
            <th>Price</th>
            <th>Submit Date</th>
        </tr>
    </thead>

    <tbody>

        @foreach($serviceCases as $index => $case)

            <tr>
                <td>{{ $index + 1 }}</td>

                <td>
                    {{ $case->companyStaff->company->name ?? '-' }}
                </td>

                <td>
                    {{ $case->companyStaff->user->name ?? '-' }}
                </td>

                <td>
                    {{ $case->service->name ?? '-' }}
                </td>

                <td>
                    {{ $case->status }}
                </td>

                <td>
                    {{ $case->price }}
                </td>

                <td>
                    {{ $case->submit_datetime }}
                </td>
            </tr>

        @endforeach

    </tbody>
</table>