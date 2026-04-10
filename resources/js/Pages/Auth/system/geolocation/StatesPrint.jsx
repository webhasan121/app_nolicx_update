import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function StatesPrint() {
    const { states = [], filters = {} } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="States Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>States Summary</p>
                        {filters?.find ? <p>Search: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />
                    <Table data={states}>
                        <thead>
                            <tr>
                                <th>SL No.</th>
                                <th>Name of State</th>
                                <th>Country Name</th>
                                <th>ISO2</th>
                                <th>ISO3</th>
                            </tr>
                        </thead>
                        <tbody>
                            {states.map((state) => (
                                <tr key={state.id}>
                                    <td>{state.sl}</td>
                                    <td>{state.name}</td>
                                    <td>{state.country_name}</td>
                                    <td>{state.iso2}</td>
                                    <td>{state.iso3166_2}</td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
