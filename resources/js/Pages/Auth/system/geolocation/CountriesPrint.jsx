import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../../Layouts/Print";
import ApplicationName from "../../../../components/ApplicationName";
import Container from "../../../../components/dashboard/Container";
import Table from "../../../../components/dashboard/table/Table";

export default function CountriesPrint() {
    const { countries = [], filters = {} } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title="Countries Summary">
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>Countries Summary</p>
                        {filters?.find ? <p>Search: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={countries}>
                        <thead>
                            <tr>
                                <th>SL No.</th>
                                <th>Name of Country</th>
                                <th>States</th>
                                <th>ISO2</th>
                                <th>ISO3</th>
                            </tr>
                        </thead>
                        <tbody>
                            {countries.map((country) => (
                                <tr key={country.id}>
                                    <td>{country.sl}</td>
                                    <td>{country.name}</td>
                                    <td>{country.states_count}</td>
                                    <td>{country.iso2}</td>
                                    <td>{country.iso3}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan="5">Total {countries.length} Items</td>
                            </tr>
                        </tfoot>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
