<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Batch Name</th>
            <th>Participant First Name</th>
            <th>Participant Email</th>
            <th>compain Name</th>
            <th>Status</th>
            <th>Sent At</th>
            <th>Description</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($batchs as $batch)
            <tr>
                <td>{{ $batch->id }}</td>
                <td>{{ optional($batch->batch)->name }}</td>
                <td>{{ optional($batch->participant)->name }}</td>
                <td>{{ optional($batch->participant)->email }}</td>
                <td>{{ optional($batch->compain)->name }}</td>
                <td>{{ $batch->status }}</td>
                <td>{{ $batch->sent_at }}</td>
                <td>{{ $batch->description }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
