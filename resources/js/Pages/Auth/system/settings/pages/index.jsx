import { Head, router } from "@inertiajs/react";
import AppLayout from "../../../../../Layouts/App";
import DangerButton from "../../../../../components/DangerButton";
import NavLink from "../../../../../components/NavLink";
import NavLinkBtn from "../../../../../components/NavLinkBtn";
import Container from "../../../../../components/dashboard/Container";
import Section from "../../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import Table from "../../../../../components/dashboard/table/Table";

export default function Index({ pages = [] }) {
    const destroy = (id) => {
        if (!window.confirm("Are you sure you want to delete this page?")) {
            return;
        }

        router.delete(route("system.pages.destroy", { id }));
    };

    return (
        <AppLayout title="Page Setup">
            <Head title="Page Setup" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div>
                                    Page Setup
                                </div>

                                <NavLinkBtn href={route("system.pages.create")}>
                                    <i className="pr-2 fas fa-plus"></i> Page
                                </NavLinkBtn>
                            </div>
                        }
                        content="Setup your necessary pages from here. add, edit and delete."
                    />

                    <SectionInner>
                        <Table data={pages}>
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th> Name </th>
                                    <th>Content</th>
                                    <th> Status </th>
                                    <th> A/C </th>
                                </tr>
                            </thead>

                            <tbody>
                                {pages.map((item) => (
                                    <tr key={item.id} className="border-b hvoer:bg-gray-50">
                                        <td>{item.id}</td>
                                        <td>
                                            <NavLink href={route("system.pages.create", { page: item.slug })}>
                                                {item.name}
                                            </NavLink>
                                            <br />
                                            <p className="text-xs">
                                                {item.title}
                                            </p>
                                        </td>
                                        <td dangerouslySetInnerHTML={{ __html: item.content }} />
                                        <td>{item.status}</td>
                                        <td>
                                            <div className="flex">
                                                <DangerButton type="button" onClick={() => destroy(item.id)}>
                                                    <i className="fas fa-trash"></i>
                                                </DangerButton>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
