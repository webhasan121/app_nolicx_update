import AppLayout from "../../../../Layouts/App";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";

export default function History({ columns = [], histories = [] }) {
    return (
        <AppLayout
            title="Star System - History"
            header={<PageHeader>Star System - History</PageHeader>}
        >
            <Container>
                <div className="flex items-center gap-2">
                    <NavLinkBtn href={route("system.levels.index")}>Levels</NavLinkBtn>
                    <NavLinkBtn href={route("system.levels.history")}>History</NavLinkBtn>
                </div>
            </Container>

            <Container>
                <Section>
                    <SectionHeader title="Level-Up History" content="" />

                    <SectionInner>
                        <div className="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                            <table className="min-w-full divide-y divide-gray-200 text-sm">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {columns.map((column, index) => (
                                            <th
                                                key={`${column}-${index}`}
                                                className="px-4 py-3 text-left font-semibold text-gray-600"
                                            >
                                                <strong>{column}</strong>
                                            </th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 bg-white">
                                    {histories.length ? (
                                        histories.map((history) => (
                                            <tr key={history.id} className="transition hover:bg-gray-50">
                                                <td className="px-4 py-3 font-medium text-gray-700">{history.sl}.</td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <strong>{history.user_name || "N/A"}</strong>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <span className="px-3 py-1 text-white bg-blue-500 hover:bg-blue-600 rounded-full">
                                                        {history.from_level_name || "N/A"}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <span className="px-3 py-1 text-white bg-purple-500 hover:bg-purple-600 rounded-full">
                                                        {history.to_level_name || "N/A"}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <span>{history.created_at_formatted || "N/A"}</span>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="px-4 py-6 text-center text-gray-500">
                                                <span>No histories found.</span>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
