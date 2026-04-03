import { usePage } from "@inertiajs/react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import UserDash from "../../../components/user/dash/UserDash";

export default function Reffer() {
    const { refs = [] } = usePage().props;

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title="VIP Ref Comission"
                        content="If your ref user purchase a vip package, then you will get the comissions."
                    />

                    <SectionInner>
                        <Table data={refs}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Comission</th>
                                    <th>User</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                {refs.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{item.comission}</td>
                                        <td>{item.user}</td>
                                        <td>{item.date}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}

